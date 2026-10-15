<!doctype html>

<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template"
  data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>HEW | Login Page</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/flag-icons.css" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css"/> -->
    <link rel="stylesheet" type="text/css" href="../../assets/css/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="../../assets/css/slick/slick-theme.css"/>
    <style>
          .image-container {
        position: relative;
        width: 100%;
        height: 100vh; /* Full screen height */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* Background Image (Blurred) */
    .authentication-image {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(10px);
        z-index: 1; /* Behind foreground image */
    }

    /* Foreground Image (Sharp) */
    .auth-cover-illustration {
        position: relative;
        z-index: 2; /* In front of the background */
        width: 200px; /* Adjust size */
        border-radius: 10px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.3); /* Attractive shadow */
    }
    </style>

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
      <div class="authentication-inner row m-0">
                     
        <!-- /Left Section -->
        <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 pb-2 ">
        <div class="image-container">
            <!-- Background Blurred Image -->
            <img
                src="{{ asset('assets/img/favicon/favicon.ico') }}"
                class="auth-cover-illustration"
                alt="auth-illustration"
            />

            <!-- Foreground Image -->
            <img
                src="{{ asset('assets/img/favicon/favicon.ico') }}"
                class="authentication-image"
                alt="mask"
            />
          </div>
        </div>
        <!-- /Left Section -->
        <!-- Login -->
        <div
          class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg position-relative py-sm-12 px-12 py-6">
          <div class="w-px-400 mx-auto pt-5 pt-lg-0">
          <h4 class="mb-1">Welcome to HEW ! 👋</h4>
            <p class="mb-5">Sign in to start your session</p>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form class="mb-5" method="POST" action="{{ route('admin.login-post') }}" id="loginForm">
            @csrf
              <div class="form-floating form-floating-outline mb-5">
                <input
                  type="text"
                  class="form-control"
                  id="email"
                  name="login"
                  placeholder="Enter your Email / Mobile"
                  autofocus />
                  <label for="email">Email / Mobile No </label>
              </div>
              
              <div class="mb-5">
                <div class="form-password-toggle">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input
                        type="password"
                        id="password"
                        class="form-control"
                        name="password"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                        aria-describedby="password" required/>
                      <label for="password">Password</label>
                    </div>
                    <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                  </div>
                  <div id="password-error" class="error text-danger mt-1"></div>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100" id="submit" type="submit">Sign in</button>
            </form>
          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../../assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <!-- <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script> -->

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script> -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="../../assets/js/slick/slick.js"></script>

    <!-- Page JS -->
    <!-- <script src="../../assets/js/pages-auth.js"></script> -->
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
                  required: "Please Fill the Email / Mobile / Username",
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
              // Force validation on all fields when form is submitted
              if (validator.numberOfInvalids()) {
                  validator.showErrors();
              }
          },
          submitHandler: function (form) {
              var submitButton = $("#submit");
              submitButton.prop("disabled", true).text("Sign In...");
              form.submit();
          }
      });
      });
  </script>
  </body>
</html>