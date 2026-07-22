<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>HR Management</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
							<!--
**Beginners project** Day-1
Inspired/Mocked from
How To Add Social Media Icons Without Images - Font Awesome icon css hover effect
link: https://www.youtube.com/watch?v=QaSLBMqPb2U&list=PL5e68lK9hEzePSLgrkx-PWMUjh64-BbIe
-->

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
</script>
        <!-- Styles -->
        <style>
            html, body {
/*                background-color: #fff;*/
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
/*                height: 100vh;*/
                margin: 0;
                background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
                background-size: 400% 400%;
                animation: gradient 15s ease infinite;
                height: 100vh;
            }
            .content {
                width: 50%;
            }
            .title.m-b-md {
                display: flex;
                flex-flow: column;
                align-items: center;
                align-self: center;
                background: #fff;
                border-radius: 25px;
                padding: 30px;
                background: #ecfffe;
            }
            @keyframes gradient {
                0% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
                100% {
                    background-position: 0% 50%;
                }
            }

            .full-height {
                height: 100vh;
            }
			ul.social_iocns{
  display: flex;
  position: relative;
  justify-content: center;
}
.new_text {
    padding: 0 125px;
}
ul.social_iocns li {
  list-style: none;
}

ul.social_iocns li a {
  width: 80px;
  height: 80px;
  background-color: #fff;
  text-align: center;
  line-height: 80px;
  font-size: 35px;
  margin: 0 10px;
  display: block;
  border-radius: 50%;
  position: relative;
  overflow: hidden;
  border: 3px solid #fff;
  z-index: 1;
}

ul.social_iocns li a .icon {
  position: relative;
  color: #262626;
  transition: .5s;
  z-index: 3;
}

ul.social_iocns li a:hover .icon {
  color: #fff;
  transform: rotateY(360deg);
}

ul.social_iocns li a:before {
  content: "";
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  height: 100%;
  background: #f00;
  transition: .5s;
  z-index: 2;
}

ul.social_iocns li a:hover:before {
  top: 0;
}

ul.social_iocns li:nth-child(1) a:before{
  background: #3b5999;
}

ul.social_iocns li:nth-child(2) a:before{
  background: #55acee;
}

ul.social_iocns li:nth-child(3) a:before {
  background: #f783ac;
}

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 40px;
            }

            .links > a {
                color: #fff;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
.job-openings {
    text-align: left;
    background: #c3073f;
    color: #fff;
    border-radius: 10px;
    padding-top: 5px;
    padding-left: 15px;
    padding-bottom: 5px;
    padding-right: 15px;
    box-shadow: 3px 5px 5px 3px #950740;
}
            .m-b-md {
                margin-bottom: 30px;
            }
			@media only screen and (max-width: 600px) {
				.title {
					font-size: 40px;
				}
				
				.content {
					margin-top: 325px;
				}
				ul.social_iocns {
					display: flex;
					position: relative;
					top: 50%;
					left: 0;
				}
			}
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
						<?php if(Auth::user()->role == "admin"){ ?>
                        <a href="{{ url('/dashboard') }}">Go to Dashboard</a>
						<?php }elseif(Auth::user()->role == "user"){ ?>
						<a href="{{ url('/user-dashboard') }}">Go to Dashboard</a>
						<?php }elseif(Auth::user()->role == "supervisor"){ ?>
						<a href="{{ url('/supervisor-dashboard') }}">Go to Dashboard</a>
						<?php }elseif(Auth::user()->role == "manager"){ ?>
						<a href="{{ url('/manager-dashboard') }}">Go to Dashboard</a>
						<?php } ?>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    <!--b >ILS</b><br>
                    <b>ilogStaffing,Inc</b><br-->
                    <img src="{{ url('public/assets/images/logo-main.png') }}" alt="logo" /> </a>
                    <!-- <b >FITS</b> -->
					<b>Time Sheet</b>
					<!--b >Contact us 1-844-255-3487</b><br-->
					


<!--ul class="social_iocns">
  <li>
    <a href="https://www.facebook.com/ilogstaffing">
      <i class="fab fa-facebook-f icon"></i>    </a>
  </li>
  <li>
    <a href="https://twitter.com/ilogstaffing"><i class="fab fa-twitter icon"></i></a>
  </li>
  <li>
    
	<a href="https://www.instagram.com/ilogstaffing/?hl=en"><i class="fab fa-instagram icon"></i></a>
  </li>
</ul-->
</div>
                <?php /*
                <div class="new_text">
                    <h2 style="    font-size: 44px;
    color: #e93b3b;
    font-weight: bold;" >Ilog is migrating from BUSINESS ONLINE PAYROLL to GUSTO payroll SOON</h2>
	
                </div>
				*/?>
				<!--div style="display:none" class="job-openings">
					<h2>JOB OPENINGS</h2>
					<h4>We are currently hiring for the following positions.<br>Call 1-844-255-3487 opt 1 and 9 </h4>
					<ul>
						<li>Certified Nursing Assistant (CNA)</li>
						<li>Certified Medical Assistant (CMA)</li>
						<li>Licensed Practical Nurse (LPN)</li>
						<li>Bachelor of Science In Nursing (BSN)</li>
						<li>Registered Nurse (RN)</li>
						<li>Certified Medication Technician (CMT)</li>
						<li>Residential Counselors. </li>
					</ul>
					<p>Upon referring friends or families, i-logstaffing will send you a gift card <br>once the person goes though the application process and is hired.Have the <br>Applicant use your last name for the "referral code".</p>
				
					
				</div-->
               <div class="job-openings">
                    <h2>Time and Attendance </h2>
                    <p>Our easy time and attendance system makes managing and collecting employee data and exporting payroll easier. It eliminates paperwork, controls costs, and increases productivity.</p>
                    <!--h2>How it works</h2>
                    <ul>
                        <li>Employers must create an account and meet with our Expect Onboarding team to help with the process. </li>
                        <li>Then, employees will gain access and register with their emails and passwords. </li>
                        <li>They gain access immediately to start adding their hours. Very easy and simple</li>
                    </ul-->
                    <!--p>Upon referring friends or families, i-logstaffing will send you a gift card <br>once the person goes though the application process and is hired.Have the <br>Applicant use your last name for the "referral code".</p-->
                    
                </div>
            </div>
        </div>
    </body>
</html>
