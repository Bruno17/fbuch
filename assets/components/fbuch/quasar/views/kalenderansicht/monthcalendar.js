import api_select from '../../components/api_select.js'
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {
    components:{
      "QCalendarMonth": QCalendarMonth.QCalendarMonth,
      api_select : api_select
    },
    props:['view'], 

    setup(props) {
      const {computed, onMounted, ref } = Vue;
      const { today, padNumber, parsed, isBetweenDates, parseTimestamp, getStartOfMonth, getEndOfMonth, parseDate, isOverlappingDates, daysBetween, indexOf } = QCalendarMonth;
      const { onBeforeRouteLeave, onBeforeRouteUpdate } = VueRouter;
      const loadedEvents = ref([]);
      const leftDrawerOpen = ref(false)
      const selectedDate = ref(getDateFromRoute())
      //const selectedType = ref(''),
      const selectedType = ref(Vue.$router.currentRoute._value.query.type||null);
      const formattedMonth = Quasar.date.formatDate(selectedDate.value, 'MMMM YYYY');
      const calendar = ref(null),
      locale = 'de-DE',
      startDate = ref(today()),
      endDate = ref(today());

      onMounted(() => {
        useLoadPermissions();
        loadMonthEvents();
      })

      /*
      const formattedMonth = computed(() => {
        return Quasar.date.formatDate(selectedDate.value, 'MMMM YYYY');
      })
      */        

      onBeforeRouteUpdate(async (to, from) => {
        // only fetch the user if the id changed as maybe only the query or the hash changed
        if (to.params.year !== from.params.year || to.params.month !== from.params.month) {
          selectedDate.value = to.params.year + '-' + to.params.month + '-' + '01'    
        }
      })      

      function getDateFromRoute(){
        let date = today();
        const params = Vue.$router.currentRoute._value.params;
        if (typeof params.year !== 'undefined' && typeof params.month !== 'undefined'){
            date = params.year + '-' + params.month + '-' + '01'      
        }
        return date;
      }

      function getWeekEvents (week, weekdays) {
          const firstDay = parsed(week[ 0 ].date + ' 00:00')
          const lastDay = parsed(week[ week.length - 1 ].date + ' 23:59')
          const eventsWeek = []
          loadedEvents.value.forEach((event, id) => {
              const startDate = parsed(event.date)
              const endDate = parsed(event.date_end)

              if (isOverlappingDates(startDate, endDate, firstDay, lastDay)) {
              const left = daysBetween(firstDay, startDate, true)
              const right = daysBetween(endDate, lastDay, true)
              eventsWeek.push({
                  id, // index event
                  left, // Position initial day [0-6]
                  right, // Number days available
                  size: week.length - (left + right), // Size current event (in days)
                  event // Info
              })
              }
          })
          const events = []
          if (eventsWeek.length > 0) {
              const infoWeek = eventsWeek.sort((a, b) => a.left - b.left)
              infoWeek.forEach((_, i) => {
              insertEvent(events, week.length, infoWeek, i, 0, 0)
              })
          }
          return events
      }

      function insertEvent (events, weekLength, infoWeek, index, availableDays, level) {
          const iEvent = infoWeek[ index ]
          if (iEvent !== undefined && iEvent.left >= availableDays) {
              // If you have space available, more events are placed
              if (iEvent.left - availableDays) {
              // It is filled with empty events
              events.push({ size: iEvent.left - availableDays })
              }
              // The event is built
              events.push({ size: iEvent.size, event: iEvent.event })
              if (level !== 0) {
              // If it goes into recursion, then the item is deleted
              infoWeek.splice(index, 1)
              }
              const currentAvailableDays = iEvent.left + iEvent.size
              if (currentAvailableDays < weekLength) {
              const indexNextEvent = indexOf(infoWeek, e => e.id !== iEvent.id && e.left >= currentAvailableDays)
              insertEvent(
                  events,
                  weekLength,
                  infoWeek,
                  indexNextEvent !== -1 ? indexNextEvent : index,
                  currentAvailableDays,
                  level + 1
              )
              } // else: There are no more days available, end of iteration
          }
          else {
              events.push({ size: weekLength - availableDays })
              // end of iteration
          }
      } 
      
      function isBetweenDatesWeek (dateStart, dateEnd, weekStart, weekEnd) {
          return (
              (dateEnd < weekEnd && dateEnd >= weekStart)
              || dateEnd === weekEnd
              || (dateEnd > weekEnd && dateStart <= weekEnd)
          )
      }        

      function onMoved(data) {
          console.log("onMoved", data);
      }

      function loadMonthEvents(){
          let ts = parseTimestamp(selectedDate.value);
          const start = getStartOfMonth(ts).date;
          const end = getEndOfMonth(ts).date;
          const data = {};
          const ajaxUrl = modx_options.rest_url + 'Dates';
          data.start = start + ' 00:00:00';
          data.end = end + ' 23:59:59';
          data.types = selectedType.value;
          axios.get(ajaxUrl,{params:data})
          .then(function (response) {
             loadedEvents.value = response.data.results;
          })
          .catch(function (error) {
              console.log(error);
          }); 
      }            
          
  function badgeClasses(computedEvent) {
    if (computedEvent.event !== undefined) {
      return {
        'my-event': true,
        'text-white': true,
        [ `bg-${ computedEvent.event.Type_colorstyle||'grey' }` ]: true,
        'rounded-border': true,
        'q-calendar__ellipsis': true
      }
    }
    return {
      'my-void-event': true
    }
  }

  function badgeStyles(computedEvent, weekLength) {
    const s = {}
    if (computedEvent.size !== undefined) {
      s.width = ((100 / weekLength) * computedEvent.size) + '%'
    }
    return s
  }            

      function onChange(data) {

      }

      function onClickDate(data) {
      const ts = data.scope.timestamp; 
      let month = (ts.month < 10) ? '0' + ts.month : ts.month;
      let day = (ts.day < 10) ? '0' + ts.day : ts.day;
      Vue.$router.push('/events/day/' + ts.year + '/' + month + '/' + day);      
      }

      function prepareRoute(date){
        const query = {}
        if (selectedType.value){
            query.type = selectedType.value;
        }
        return {name:'monthcalendar',
            params:{
                year:Quasar.date.formatDate(date, 'YYYY'),
                month:Quasar.date.formatDate(date, 'MM')
            },
            query: query    
        }      
      }       

      function onClickDay(data) {
      //console.log("onClickDay", data);
      }

      function onClickHeadDay(data) {
      //console.log("onClickHeadDay", data);
      }

      function setTypeFilter () {
        //console.log(selectedType.value);
        Vue.$router.push(prepareRoute(selectedDate.value));
      }      

      function onToday() {
        const newDate = new Date;
        Vue.$router.push(prepareRoute(newDate));    
      }

      function onPrev() {
        const newDate = Quasar.date.subtractFromDate(selectedDate.value,{month:1});
        Vue.$router.push(prepareRoute(newDate));    
      }

      function onNext() {
        //calendar.value.next();
        const newDate = Quasar.date.addToDate(selectedDate.value,{month:1});
        Vue.$router.push(prepareRoute(newDate));      
      }

      return {
        selectedDate,
        calendar, // ref
        onMoved,
        onChange,
        onClickDate,
        onClickDay,
        onClickHeadDay,
        onToday,
        onPrev,
        onNext,
        formattedMonth,
        loadedEvents,
        getWeekEvents,
        badgeClasses,
        badgeStyles,
        selectedType,
        setTypeFilter,
        useHasPermission
      }
    },
    template: '#monthcalendar-view' 
    
    // or `template: '#my-template-element'`
  }
  