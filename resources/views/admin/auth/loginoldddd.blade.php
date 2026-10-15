<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harshad Safety Valve Manufacturer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100..900&display=swap" rel="stylesheet">

    <link rel="icon" type="image/jpg" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">

    <link rel="stylesheet" type="text/css" href="../../assets/css/hew/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/hew/FontAwesome/css/all.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/hew/style.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/hew/responsive.css">
</head>

<body>
    <section class="main p-0">
        <div class="login">
            <div class="left">
                <img src="{{ asset('assets/img/images/login-left-bg.jpg') }}" />
            </div>
            <div class="right">
                <div class="login-div">
                    <div class="logo text-center">
                        <img src="{{ asset('assets/img/images/logo.png') }}" class="logo-img" />
                    </div>
                    <div class="mobile-img">
                        <img src="{{ asset('assets/img/images/login-mobile-bg.png') }}" />
                    </div>
                    <h4>Admin Panel</h4>
                    <!-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                    @endif -->
                    <form class="mx-auto" method="POST" action="{{ route('admin.login-post') }}" id="loginForm">
                        @csrf
                        <div class="form-floating">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="form-floating mb-3">
                            <input name="login" type="email" class="form-control" id="loginID" placeholder="name@example.com">
                            <label for="loginID">Login ID</label>
                        </div>
                        <div class="form-floating position-relative">
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                            <label for="password">Password</label>
                            <span href="" class="eye-btn"><i class="fa-solid fa-eye"></i></span>
                            <div id="password-error" class="error text-danger mt-1"></div>
                            <!-- <a href="" class="eye-btn"><i class="fa-solid fa-eye-slash"></i></a> -->
                        </div>
                        <!-- <a href="new-tag.html" class="btn btn-login">SIGN IN</a> -->
                         <button class="btn btn-login d-grid w-100" id="submit" type="submit">SIGN IN</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- <script type="text/javascript" src="js/jquery-3.7.1.min.js"></script> -->
    <script type="text/javascript" src="{{ asset('assets/js/hew/jquery-3.7.1.min.js') }}"></script>
    <!-- <script type="text/javascript" src="js/bootstrap.bundle.js"></script> -->
    <script type="text/javascript" src="{{ asset('assets/js/hew/bootstrap.bundle.js') }}"></script>
    <!-- <script type="text/javascript" src="css/FontAwesome/js/pro.js"></script> -->
    <script type="text/javascript" src="../../assets/css/hew/FontAwesome/js/pro.js"></script>
    <!-- <script type="text/javascript" src="js/script.js"></script> -->
    <script type="text/javascript" src="{{ asset('assets/js/hew/script.js') }}"></script>
</body>
 <script src="{{asset('assets/vendor/js/jquery-validation.js')}}"></script>
    <script>
    $(document).ready(function () {
    // Initialize jQuery Validation
      $("#loginForm").validate({
          rules: {
              login: {
                  required: true,
                  normalizer: function(value) {
                      return $.trim(value);
                  }
              },
              password: {
                  required: true
              }
          },
          messages: {
              login: {
                  required: "Please Fill the Email",
              },
              password: {
                  required: "Please Fill the password"
              }
          },
          errorElement: "div",
          errorPlacement: function (error, element) {
              if (element.attr("id") === "password") {
                  $("#password-error").empty().append(error);  
              } else {
                  error.addClass("text-danger mt-1");
                  error.insertAfter(element);
              }
          },
          invalidHandler: function(event, validator) {
              if (validator.numberOfInvalids()) {
                  validator.showErrors();
              }
          },
          submitHandler: function (form) {
              var submitButton = $("#submit");
              submitButton.prop("disabled", true).text("SIGN IN...");
              form.submit();
          }
      });
      });
  </script>
  <script>
    $(document).ready(function () {
        $('.eye-btn').on('click', function (e) {
            e.preventDefault();

            const passwordField = $('#password');
            const icon = $(this).find('i');

            // Toggle the type attribute
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash'); // Change icon
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye'); // Revert icon
            }
        });
    });
</script>
</html>