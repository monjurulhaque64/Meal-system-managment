$(document).ready(function(){
    
    // ১. ফিল্টার রিপোর্ট জেনারেট করা
    $(document).on('click', '#user_log', function(){
        var date_sel_start = $('#date_sel_start').val();
        var date_sel_end = $('#date_sel_end').val();
        var time_sel = $(".time_sel:checked").val();
        var time_sel_start = $('#time_sel_start').val();
        var time_sel_end = $('#time_sel_end').val();
        var card_sel = $('#card_sel option:selected').val();
        var dev_uid = $('#dev_sel option:selected').val();
        
        $.ajax({
          url: 'user_log_up.php',
          type: 'POST',
          data: {
            'log_date': 1,
            'date_sel_start': date_sel_start,
            'date_sel_end': date_sel_end,
            'time_sel': time_sel,
            'time_sel_start': time_sel_start,
            'time_sel_end': time_sel_end,
            'card_sel': card_sel,
            'dev_uid': dev_uid,
            'select_date': 0
          },
          success: function(data){
            $('.up_info2').fadeIn(500).text("The Filter has been selected!");
            $('#userslog').html(data);
            $('#Filter-export').modal('hide');
            setTimeout(function () { $('.up_info2').fadeOut(500); }, 5000);
          }
        });
    });

    // ২. ডিলিট বাটন ফাংশন (Event Delegation ব্যবহার করা হয়েছে)
    $(document).on('click', '.delete_log', function(e){
        e.preventDefault();
        var el = this;
        var deleteid = $(this).attr('data-id'); // ID গেট করা

        if(confirm("আপনি কি নিশ্চিতভাবে এই রেকর্ডটি ডিলিট করতে চান?")){
            $.ajax({
                url: 'log_up_manage.php',
                type: 'POST',
                data: { 'del_id': deleteid },
                success: function(response){
                    // ডেবাগিং অ্যালার্ট (সফল হলে ১ দেখাবে)
                    if(response.trim() == "1"){
                        $(el).closest('tr').fadeOut(600, function(){
                            $(this).remove();
                        });
                    } else {
                        alert("সার্ভার রেসপন্স: " + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert("AJAX Error: ফাইলটি পাওয়া যায়নি!");
                }
            });
        }
    });
});