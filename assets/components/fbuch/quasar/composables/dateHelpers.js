export function useNewDateFromDateAndTime(date,time){
    let datestring = Quasar.date.formatDate(date, 'YYYY-MM-DD');
    datestring = datestring + ' ' + time;
    return new Date(datestring);    
}

export function useSetDateDiff(props) {
    let startfield = props.startfield || false;
    let timestartfield = props.timestartfield || false;
    let endfield = props.endfield || false;
    let timeendfield = props.timeendfield || false;
    let event = props.event || false;
    let startdate = new Date();
    let enddate = new Date();
    let starttime = '00:00';
    let endtime = '00:00';
    if (!startfield) {
        startfield = 'date';
    }
    if (!timestartfield) {
        timestartfield = 'start_time';
    }
    if (!endfield) {
        endfield = 'date_end';
    } 
    if (!timeendfield) {
        timeendfield = 'end_time';
    }
    if (!event) {
        event = {};
        event[startfield] = startdate;
        event[endfield] = enddate;
        event[timestartfield] = starttime;
        event[timeendfield] = endtime;
    } 
    startdate = event[startfield] || startdate; 
    enddate = event[endfield] || enddate;
    starttime = event[timestartfield] || starttime;
    endtime = event[timeendfield] || endtime;         
    const date1 = useNewDateFromDateAndTime(startdate,starttime);
    const date2 = useNewDateFromDateAndTime(enddate,endtime);
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
}  