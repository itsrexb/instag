angular
  .module('instag-app')
  .factory('timeAgoService',timeAgoService);

function timeAgoService() {
  return {
    timeAgo: timeAgo,
    historyMarkup: historyMarkup
  }

  /*
  timeAgo() - Calculates how much time has passed
  Function arguments:
  added = element added time
  offset = user configuration GTM offset
  */
  function timeAgo(added,offset) {
      // Get current time and calculate his offset value, same for element added time
      var now = new Date();
      var nowOffset = now.getTime() + (now.getTimezoneOffset()*60000);
      var addedOffset = (added-(parseInt(offset)*1000));
      var time = Math.round((nowOffset-addedOffset)/1000);
      // Check for time value
      switch (true) {
        case time < 60:
          time = document.getElementById('timeago-seconds').dataset.language;
          break;
        case time >= 60 && time < 120:
          time = document.getElementById('timeago-minute').dataset.language;
          break;
        case time >= 120 && time < 3600:
          time = Math.round(time/60).toString() + document.getElementById('timeago-minutes').dataset.language;
          break;
        case time >= 3600 && time <7200 :
          time = document.getElementById('timeago-hour').dataset.language;
          break;
        case time >= 7200 && time < 86400:
          time = Math.round((time/60)/60).toString() + document.getElementById('timeago-hours').dataset.language;
          break;
        case ((time/60)/60) >= 24 && ((time/60)/60) < 48:
          time = document.getElementById('timeago-day').dataset.language;
          break;
        case ((time/60)/60) >= 48:
          time = Math.round(((time/60)/60)/24).toString() + document.getElementById('timeago-days').dataset.language;
          break;
        default:
          time = '';
          break;
      }
      return time;
  }

  /* 
    historyMarkup() - Defines history items markup for account acctivity
    Function arguments:
    events = events elements
    singleEvent = defines if it's only one element
  */
  function historyMarkup(events,singleEvent){
    // Get singleEvent value
    var singleEvent = singleEvent || false;
    // Get offset value and initialize markup variables
    var offset = angular.element(document.getElementById('history-content')).attr('data-offset');
    var items = '';
    var time = '';
    // Check for a single event
    if (singleEvent) {
      // Save event data
      events.length = 1;  
      var added = new Date(events.date_added);
      var code = events.code;
      var title = events.title;
      var description = events.description;
    }
    // Loop through events elements
    for (var i=0;(i<events.length && i < 15);i++) {
      // If there's more than one element, save event data
      if (events.length > 1) {
        var added = new Date(events[i].date_added);
        var code = events[i].code;
        var title = events[i].title;
        var description = events[i].description;
      }
      // Get Time using timeAgo()
      time = timeAgo(added,offset);
      items += '<div class="history-item sidebar-item">';
      items += '<i class="fa fa-event-'+code+' history-icon"></i>';
      items += '<div class="item-content">';
      items += '<h4>'+title+'</h4>';
      items += '<p>'+description+'</p>';
      items += '<small data-time="'+added+'">'+time+'</small>';
      items += '</div>';
      items += '</div>';
    }
    return items;
  }

} 