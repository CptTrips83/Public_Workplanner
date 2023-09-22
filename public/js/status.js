function getStatus()  {
    $.ajax({
        url:		routeStatus,
        type:		'POST',
        dataType:	'json',
        async:		true,

        success: successStatus,
        error: function(xhr, textStatus, errorThrown){
            //alert('Ajax request failed.');
        }
    })
}

    function successStatus(data, status) {
        
        var text = "";
        
        if(!data['workEntryAktiv'])
        {
            text += 'Sie sind nicht eingestempelt.';
        }
        else
        {
            text += 'Sie sind eingestempelt seit ' + data['workEntryStartDatum'] + ' um ' + data['workEntryStartZeit'];
            if(data['pauseAktiv'])
            {
                text += ' | ' + data['pauseKategorie'] + ' aktiv seit ' + data['pauseStartDatum'] + ' um ' + data['pauseStartZeit'];
            }
        }

        $('#status').html('<h5>' + text + '</h5>');
    }