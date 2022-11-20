export function useNewDateFromDateAndTime(date,time){
    let datestring = Quasar.date.formatDate(date, 'YYYY-MM-DD');
    datestring = datestring + ' ' + time;
    return new Date(datestring);    
}

export function useSetDateDiff(props) {
    const date1 = useNewDateFromDateAndTime(props.event[props.startfield],props.event[props.timestartfield]);
    const date2 = useNewDateFromDateAndTime(props.event[props.endfield],props.event[props.timeendfield]);
    const diff = {};
    diff.minutes_total = Quasar.date.getDateDiff(date2, date1, 'minutes');
    let hours = Math.floor(diff.minutes_total/60);
    diff.days = Math.floor(hours/24);
    diff.minutes = diff.minutes_total - (hours*60);
    diff.hours = hours-(diff.days*24);
    props.state.days = diff.days;
    props.state.hours = diff.hours;
    props.state.minutes = diff.minutes;
    props.state.minutes_total = diff.minutes_total;
    //console.log('useSetDateDiff',props,diff);
  }

export function useSetEndDate(props) {
    let newDate = useNewDateFromDateAndTime(props.event[props.startfield],props.event[props.timestartfield]);
    newDate = Quasar.date.addToDate(newDate, { minutes: props.state.minutes_total});    
    props.event[props.endfield] = Quasar.date.formatDate(newDate, 'YYYY-MM-DD');
    props.event[props.timeendfield] = Quasar.date.formatDate(newDate, 'HH:mm');    
}  

export function useGetWeekStart(date) {
    const weekStartAndEnd = {};
    const newDate = useNewDateFromDateAndTime(date,'00:00');
    const dayOfWeek = Quasar.date.getDayOfWeek(newDate); 
    return Quasar.date.subtractFromDate(newDate, { days: dayOfWeek-1 });
    /*
    weekStartAndEnd.end = Quasar.date.addToDate(newDate, { days: 7-dayOfWeek });
    console.log(weekStartAndEnd);
    */
}  