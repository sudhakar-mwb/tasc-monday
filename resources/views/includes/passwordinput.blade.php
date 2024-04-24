<div class="input-group flex-nowrap" id="password-filled">
  <input class="form-control" id="input-password" type="password" placeholder="Password" name="password">
  <span class="input-group-text fs-5 encrypted" style="cursor:pointer" id="controller"><i
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