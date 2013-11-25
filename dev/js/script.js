// Syntax highlighting
prettyPrint();

// Clickable rows
$('.clickable-row').on('click', function(){
    var url = $(this).data('href');
    window.location = url;
});

// Filter datasets and collections
$('#dataset-filter').on('keyup', function(){
    var filter = $(this).val();
    $('.empty .panel').addClass('hide');

    if(filter){
        $('.dataset-filter').html(filter);
        filter = new RegExp(filter, 'i');
        var results = false;

        // Set filter
        $('.dataset').each(function(){
            var dataset = $(this);
            var title = $('.dataset-title', dataset).text();
            var description = $('.dataset-description', dataset).text();

            // Check if we can find a match
            if(title.match(filter) || description.match(filter)){
                results = true;
                dataset.removeClass('hide');
                dataset.addClass('visible');
            }else{
                dataset.addClass('hide');
            }

        });

        // Show 'no results' message
        if(!results){
            $('.empty .panel').removeClass('hide');
        }

    }else{
        // Clear filter
        $('.dataset').removeClass('hide');
    }
});