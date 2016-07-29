$(function(){

    // Instantiate MixItUp:
    $('#Container').mixItUp({
        animation: {
            duration: 520,
            effects: 'fade scale(0.01) stagger(1ms)',
            easing: 'cubic-bezier(0.55, 0.085, 0.68, 0.53)'
        }
    });

    $('#Container').mixItUp({
        layout: {
            containerClass: 'list'
        }
    });

    $('.special').addClass( "simptip-position-bottom simptip-smooth" ).attr('data-tooltip', 'Tooltips content');

    function downLink(){}

    $( ".mix" ).not(".special").click(function(e) {
        ip = e.target.innerText;
        $("#curIp").text(ip);
    });
});