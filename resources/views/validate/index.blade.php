@extends('layouts.app', ['activePage' => 'reportmgmt', 'title' => 'Reporting Portal', 'titlePage' => __('Report Management')])

<style>
#login {
        width: auto;
}
.login-form {
    width: 375px !important;
    text-align: left;
    margin: 10% auto !important;
}

.login-form h4 {
    line-height: 50px;
    color: rgba(0, 166, 202, 1);
    text-shadow: 1px 1px 0 rgba(255, 255, 255, 1);
}

.login-form {
    width: 350px;
    margin: auto;
    position: relative;
    border: 2px solid rgba(0, 166, 202, 1);
    border-radius: 5px;
}

.header {
	padding: 30px;
}

.login-form .content .input {
        width: 316px;
}

.login-form .content {
	padding: 0px 30px 25px 30px;
        padding-bottom: 0px !important;
}

.login-form .footer {
        padding-top: 35px !important;
	padding: 15px 25px !important;
    	overflow: auto !important;
}

.login-form input[type="submit"], .changepwd {
    float: right;
    color: #000;
    text-align: left;
    font-size: 16px;
    background-color: #FCD800;
    padding: 6px 18px 6px 18px;
    border: none;
    cursor: pointer;
}

.main-panel .header {
	margin-bottom: 0px !important;
}

</style>

@section('content')
<div class="content">
        <div id="login">
                <form name="login-form" class="login-form" action="/validatelogin" method="post">
	        @csrf
                        <div class="header">
                                <h4>Pin Code Verification</h4>
                                <span>To continue you will need to verify Pin Code</span>
                        </div>
                        <div class="content">
                                <input name="code" autofocus="autofocus" type="text" class="input username" placeholder="PIN CODE" />
                        </div>
                        <p style="font-size: 13px;color: red;padding: 10px 35px 0px 35px !important;"><?php echo $errors; ?></p>
                        <div class="footer" style="width: 100%;display: block;">
                                <div style="float: right;"><input type="submit" name="verify_code" value="Verify" class="button"  /></div>
                                <div style="float: right;"><input type="submit" name="resend_code" value="Resend Code" class="button" style="margin-right: 10px;" /></div>
				<div style="clear: both;"></div>
                        </div>
                </form>
        </div>
</div>
@endsection

