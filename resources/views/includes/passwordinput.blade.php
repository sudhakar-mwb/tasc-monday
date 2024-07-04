<div class="input-group flex-nowrap" id="password-filled" style="background: #ececec;
    border: 0;
    border-radius: 50px;
    ">
  <input class="form-control" id="input-password" type="password" placeholder="Password" name="password" style="background: #e8f0fe;
    border: 0;
    border-top-left-radius: 50px;
        border-bottom-left-radius: 50px;
    padding: 10px 0px 10px 15px;">
  <span class="input-group-text fs-5 encrypted" style="cursor:pointer;border-top-right-radius:50px;border-bottom-right-radius:50px;background: #f8f9fa;display:flex;justify-content:center;align-items:center;" id="controller"><i
          class="bi bi-eye-slash-fill"></i></span>
</div>
<script>
  $('#controller').on('click', function(e) {
      if ($(this).hasClass('encrypted')) {
          $(this).html('<i class="bi bi-eye-fill"></i>');
          $(this).removeClass('encrypted');
          $('#password-filled #input-password').attr('type', 'text');

      } else {
          $(this).html('<i class="bi bi-eye-slash-fill"></i>');
          $(this).addClass('encrypted');
          $('#password-filled #input-password').attr('type', 'password');
      }
  })
</script>