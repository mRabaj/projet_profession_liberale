<?php

require_once("class/dao.php");


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.0/main.min.css">
    <script  src="https://code.jquery.com/jquery-3.5.1.min.js"  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
     <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.0/main.min.js"></script>
    <script type="text/javascript">
        var calendar;
        $(document).ready( function () {
                                
                         calendarEl = document.getElementById('calendar');
                            calendar = new FullCalendar.Calendar(calendarEl, {    
                            initialView: 'timeGridWeek',
                            locale:'fr',
                            hiddenDays: [ 0 ],
                            slotDuration: '00:20:00',
                            slotMinTime: '8:00', 
                            slotMaxTime: '18:00',
                            // String, default: 'standard'
                            selectable:true,
                            editable:true,
                            buttonText:{
                            today:    'today',
                            month:    'month',
                            week:     'week',
                            day:      'day',
                            list:     'list'                   
                            },                              
                            headerToolbar: {
                            left: 'prev next',
                            center: 'title',
                            right: 'today'
                            },                        
                            events:'myxmlfeed.php',
                            dateClick: function(info) {
                                                                                    
                                $('#date-of-appointment').val(info.dateStr.split('T')[0]);                                   
                                $('#hour-of-appointment').val(info.dateStr.split('T')[1].split('+')[0]);
                                $('#exampleModal').modal('show');   
                            },                             
                            eventClick: function(info) {
                              console.log(info);
                              // alert('Event: ' + info.event.title);
                              // alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
                              // alert('View: ' + info.view.type);

                              $('#exampleModal').modal('show'); 

                              // change the border color just for fun
                              info.el.style.borderColor = 'red';
                            }, 
                        });
                      calendar.render(); 
        });
    </script>

</head>
<body>  

    <!-- Calendar -->
    <div id='calendar'></div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form class="row g-3 needs-validation" novalidate > 
        <div class="col-md-7">
        <label for="validationCustom04" class="form-label">Patients</label>
                    <select name="patient" id="patient" class="form-control " required>
                        <?php 
                        foreach($dao->getPatient() as $item){
                            ?>
                            <option value="<?= $item['id'] ?>"><?= $item["nom"]." ".$item["prenom"]  ?></option>
                        <?php
                        } 
                        ?>
                    </select>
        </div>
        <div class="col-md-7 position-relative">
            <label for="validationTooltip01" class="form-label">Date de rendez-vous</label>
            <input type="date" class="form-control" name="date-of-appointment" id="date-of-appointment" value="" required>
        
        </div>
        <div class="col-md-7 position-relative">
            <label for="validationTooltip02" class="form-label">Heure de rendez-vous</label>
            <input type="time" class="form-control" name="hour-of-appointment" id="hour-of-appointment" value="" required>
        
        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="insertAppointment()" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
  <script>
    function insertAppointment(){
          	//initialisation HTTPRequest
            var xhttp = new XMLHttpRequest();
                    //on lui affecte une fonction quand HTTPREQUEST reçoit des informations
                      xhttp.onreadystatechange = function() {
                        //vérification que la requête HTTP est effectuée (readyState 4) et qu'elle s'est bien passée (status 200)
                        if (this.readyState == 4 && this.status == 200) {
                          alert(xhttp.responseText.split("}")[1]);
                          
                             $('#exampleModal').modal('hide');
                          calendar.refetchEvents();
                                             
                         
                                               
                        }
                      };
                      xhttp.open("GET","class/insertappointment2.php?idPraticien=2&idPatient="+document.getElementById('patient').value+"&date_heure_debut="+document.getElementById('date-of-appointment').value+" "+document.getElementById('hour-of-appointment').value, true);
                      xhttp.send();  
                         
    }
  </script>
</body>
</html>