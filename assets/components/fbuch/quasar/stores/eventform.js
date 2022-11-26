const { ref } = Vue;

export const useRecurrenciesStore = Pinia.defineStore('recurrencies',() => {
    const monthlyEvents = ref({});
    const recurrencesDates = ref({});
    const recurrenceSelections = ref({});

    function formatEvent(event){
        event['formattedDate'] = Quasar.date.formatDate(event.date, 'dd DD. MM. YYYY ' + event.start_time + ' - ');
        if (Quasar.date.isSameDate(event.date, event.date_end,'days')) {
          event['formattedDate'] += event.end_time;
          event['formattedEndDate'] = '';     
        } else {
          event['formattedEndDate'] = Quasar.date.formatDate(event.date_end, 'dd DD. MM. YYYY ' + event.end_time);  
        }        
    }

    function prepareEvents(events){
        monthlyEvents.value={}; 
        recurrencesDates.value=[];
        const preparedEvents = {};
        let prevmonth = '0';
        let month = '0';
        events.forEach((event, id) => {
          month = Quasar.date.formatDate(event.date, 'YYYY-MM');
          if (month != prevmonth){
            preparedEvents[month] = {};
            preparedEvents[month] = {};
            preparedEvents[month]['events'] = [];
            preparedEvents[month]['month'] = month;
            preparedEvents[month]['formattedmonth'] = Quasar.date.formatDate(event.date, 'MMMM YYYY');
            prevmonth = month;
          }
          formatEvent(event);
          recurrenceSelections.value[event.id]=true;  
          preparedEvents[month]['events'].push(event);
          recurrencesDates.value.push(Quasar.date.formatDate(event.date, 'YYYY/MM/DD'));
    
        })
        monthlyEvents.value = preparedEvents;
      }    

    function loadEvents(props){
        const data = {};
        if (props.parent){
          data.parent = props.parent;
        }
        if (props.showhidden){
          data.show_hidden = props.showhidden;
        }                 
        const ajaxUrl = modx_options.rest_url + 'Dates';
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           prepareEvents(response.data.results);
           //console.log(monthlyEvents.value);
        })
        .catch(function (error) {
            console.log(error);
        }); 
      }
  
    return { loadEvents,monthlyEvents,recurrencesDates,recurrenceSelections }
  })