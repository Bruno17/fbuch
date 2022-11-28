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
          //console.log('getWeekEvents' , week);
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
    template: `
    <div id="kalender" style="min-height: 100vh;">
    <div class="q-pa-md q-gutter-sm row">
      <q-btn outline label="< Früher" @click="onPrev"></q-btn>
      <q-btn outline label="Heute" @click="onToday"></q-btn>
      <q-btn outline label="Später >" @click="onNext"></q-btn>

      <api_select
      clearable
      v-model="selectedType"
      label="Termin Art filtern"
      controller="Datetypes?limit=100000&returntype=options"
      @update:model-value="setTypeFilter"
      >
      </api_select>  
      <q-space/>
      <q-avatar color="blue" class="text-white" icon="question_mark"/>
    </div>
  
    <div class="q-pa-md full-width" style="height: 400px;">
    <div class="text-h4 text-center"> {{ formattedMonth }} </div>
      <q-calendar-month 
  ref="calendar" 
  v-model="selectedDate" 
  date-align="left"
  showDayOfYearLabel
  showMonthLabel="false"
  :day-min-height="70" 
  focusable hoverable bordered 
  @change="onChange" 
  @moved="onMoved" 
  @click-date="onClickDate" 
  @click-day="onClickDay" 
  @click-head-day="onClickHeadDay" 
  locale="de"
  :weekdays="[1,2,3,4,5,6,0]"
  >
  <template #day-of-year="{ scope: { timestamp } }">
      <template v-if="useHasPermission('fbuch_edit_termin')">
        <q-btn flat round padding="none" icon="edit" :to="'/events/day/' + timestamp.year + '/' + timestamp.month + '/' + timestamp.day"/>
      </template>
      <template v-else>
        <q-btn flat round padding="none" color="primary" icon="info" :to="'/events/day/' + timestamp.year + '/' + timestamp.month + '/' + timestamp.day"/>
      </template>
  </template>
  
            <template #week="{ scope: { week, weekdays } }">
              <template
                v-for="(computedEvent, index) in getWeekEvents(week, weekdays)"
                :key="index"
              >
                <div
                  :class="badgeClasses(computedEvent)"
                  :style="badgeStyles(computedEvent, week.length)"
                >
                  <div
                    v-if="computedEvent.event && computedEvent.event.title"
                    class="title q-calendar__ellipsis"
                  >
                    {{ computedEvent.event.title }}

                  </div>
                  <q-tooltip 
                  v-if="computedEvent.event && computedEvent.event.title" 
                  >
<pre style="white-space: pre-wrap; word-wrap: break-word;font-family: inherit;">{{ computedEvent.event.title  }} {{ computedEvent.event.start_time}} 
{{ computedEvent.event.description }}</pre>
                  </q-tooltip>                  
                </div>
              </template>
            </template>
  
  
      </q-calendar-month>
    </div>
  </div>
    `
    // or `template: '#my-template-element'`
  }
  