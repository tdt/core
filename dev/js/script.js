// Syntax highlighting
prettyPrint();

// Clickable rows
$('.clickable-row').on('click', function(){
    var url = $(this).data('href');
    window.location = url;
});

$(document).ready(Filter.init);
