
            <!--====== Section 1 ======-->
            <div class="u-s-p-y-60">

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="breadcrumb">
                            <div class="breadcrumb__wrap">
                                <ul class="breadcrumb__list">
                                    <li class="has-separator">

                                        <a href="index.php">Home</a></li>
                                    <li class="is-marked">

                                        <a href="index.php?sign-up">Signup</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--====== End - Section 1 ======-->


            <!--====== Section 2 ======-->
            <div class="u-s-p-b-60">

                <!--====== Section Intro ======-->
                <div class="section__intro u-s-m-b-60">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="section__text-wrap">
                                    <h1 class="section__heading u-c-secondary">CREATE AN ACCOUNT</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Intro ======-->


                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="row row--center">
                            <div class="col-lg-6 col-md-8 u-s-m-b-30">
                                <div class="l-f-o">
                                    <div class="l-f-o__pad-box">
                                        <h1 class="gl-h1">USER ACCOUNT TYPE</h1>
                                        <div class="row" style="padding: 5%;">
                                        <div class="radio-box col-lg-6 col-md-6">
                                            <input type="radio" checked id="customer" name="user_account" onclick="check_user_account();" onchange="check_user_account();">
                                            <div class="radio-box__state radio-box__state--primary">
                                            <label class="radio-box__label" for="customer" style="font-size: 100%;">CUSTOMER</label></div>
                                        </div>
                                        <div class="radio-box col-lg-6 col-md-6">
                                            <input type="radio" id="seller" name="user_account" onclick="check_user_account();" onchange="check_user_account();">
                                            <div class="radio-box__state radio-box__state--primary">
                                            <label class="radio-box__label" for="seller" style="font-size: 100%;">SELLER</label></div>
                                        </div>
                                        </div>

                                        <h1 class="gl-h1">PERSONAL INFORMATION</h1>
                                        <form class="l-f-o__form" method="post" action="#/" onsubmit="signup();return false;">
                                            <input type="text" id="user_type" style="display: none;" value="CUSTOMER">
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">FIRST NAME (OR BUSINESS NAME) *</label>

                                                <input class="input-text input-text--primary-style" type="text" id="first_name" placeholder="First Name" required></div>
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">LAST NAME (OR BUSINESS NICK NAME) *</label>

                                                <input class="input-text input-text--primary-style" type="text" id="last_name" placeholder="Last Name" required></div>
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">E-MAIL</label>

                                                <input class="input-text input-text--primary-style" type="email" id="email" placeholder="Enter E-mail"></div>
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">PHONE NUMBER *</label>

                                                <input class="input-text input-text--primary-style" type="text" id="phone_no" placeholder="Phone Number (07...)" maxlength="10" minlength="10" onkeypress="return isNumber(event)" required></div>
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">BIRTHDAY *</label>

                                                <input class="input-text input-text--primary-style" type="date" id="dob"></div>
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">USERNAME *</label>

                                                <input class="input-text input-text--primary-style" type="text" id="username" placeholder="Enter Username"></div>
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">PASSWORD *</label>

                                                <input class="input-text input-text--primary-style" type="password" id="password" placeholder="Enter Password"><span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span></div>
                                            <div class="u-s-m-b-30">

                                                <label class="gl-label">CONFIRM PASSWORD *</label>

                                                <input class="input-text input-text--primary-style" type="password" id="conf_password" placeholder="Confirm Password"><span toggle="#conf_password" class="fa fa-fw fa-eye field-icon toggle-password"></span></div>
                                            <div class="u-s-m-b-15">

                                                <input type="text" id="result_response" style="display: none;">
                                                <button class="btn btn--e-transparent-brand-b-2" type="submit">CREATE</button></div>

                                            <a class="gl-link" href="index.php">Return to Store</a> - <a class="gl-link" href="index.php?sign-in">Return to sign in</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Content ======-->
            </div>
            <!--====== End - Section 2 ======-->
