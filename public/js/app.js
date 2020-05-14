//Research on books
$(document).on('click', '.searchBook', function(){
    $( ".result-book" ).remove();
    var value=$(this).prev().val(); 
    var field=$(this).prev().data("field");
    //Allows to not get a 404 when input's search is empty
    if (value === '') {var value = 'nullValue';}
    $.ajax({
        url:"/BEB/public/admin/book/search/"+field+"-"+value,
        type: "POST",
        dataType: "json",
        data: {
            "field": field,
            "value": value
        },
        async: true,
        success: function (data)
        {
            $('#table-book').append(data.books);
        }
    });
    return false;
});

//Show research inputs
$(document).on('click', '.logo-search', function(){
    var field=$(this).data("field");
    if($(".search-container-"+field+" " ).css('display') === "none")
    {
        $(".search-container-"+field+" " ).css('display','block');
    }
    else{
       $(".search-container-"+field+" " ).css('display','none');
    }
});

//Research on pupils
$(document).on('click', '.searchPupil', function(){
    $( ".result-pupil" ).remove();
    var value=$(this).prev().val(); 
    var field=$(this).prev().data("field");
    //Allows to not get a 404 when input's search is empty
    if (value === '') {var value = 'nullValue';}
    $.ajax({
        url:"/BEB/public/admin/pupil/search/"+field+"-"+value,
        type: "POST",
        dataType: "json",
        data: {
            "field": field,
            "value": value
        },
        async: true,
        success: function (data)
        {
            $('#table-pupil').append(data.pupils);
        }
    });
    return false;
});

//Research on borrows
$(document).on('click', '.searchBorrow', function(){
    $( ".result-borrow" ).remove();
    var value=$(this).prev().val(); 
    var field=$(this).prev().data("field");
    //Allows to not get a 404 when input's search is empty
    if (value === '') {var value = 'nullValue';}
    $.ajax({
        url:"/BEB/public/admin/borrow/search/"+field+"-"+value,
        type: "POST",
        dataType: "json",
        data: {
            "field": field,
            "value": value
        },
        async: true,
        success: function (data)
        {
            $('#table-borrow').append(data.borrows);
        }
    });
    return false;
});