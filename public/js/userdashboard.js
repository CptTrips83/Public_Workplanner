    var content = '';
    
    function getData() {
        $.ajax({
            url:		routeDashboard,
            type:		'POST',
            dataType:	'json',
            async:		true,

            success: successDashboard,
            error: function(xhr, textStatus, errorThrown){
                //alert('Ajax request failed.');
            }
        })
    }

    function successDashboard(data, status) {  
        content = '';

        $.each(data, createRow)
        
        $('#tableDashboard').html(content);
    }

    function createRow(i, item){
        var stempeluhrStatus = 'Nicht Eingestempelt';
        var status = 'Nicht Eingestempelt';

        if(item['workEntryAktiv'] == true){
            stempeluhrStatus = 'Eingestempelt' + ' seit ' + item['workEntryStartDatum'] + ' ' + item['workEntryStartZeit'];
            status = 'Arbeitet';
        
            if(item['pauseAktiv'] == true)
                status = item['pauseKategorie'] + ' seit ' + item['pauseStartDatum'] + ' ' + item['pauseStartZeit'];
        }
        content += '<tr>';

        content += '<td>' + item['nachname'] + ', ' + item['vorname'] + '(' + item['userId'] + ')</td>';
        content += '<td>' + stempeluhrStatus + '</td>';
        content += '<td>' + status + '</td>';

        content += '</tr>';
    }