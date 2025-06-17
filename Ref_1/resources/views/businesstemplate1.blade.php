<h1>Hi, {{ $businessName }}</h1>
<p> <b>{{$username}}</b> had shown interest in your {{$job_name}} job post.</p>
Thanks & Regards,
Bizlx
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="x-apple-disable-message-reformatting">
	<title></title>
	<!--[if mso]>
	<noscript>
		<xml>
			<o:OfficeDocumentSettings>
				<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
	</noscript>
	<![endif]-->
	<style>
		table, td, div, h1, p {font-family: Arial, sans-serif;}
	</style>
</head>
<body style="margin:0;padding:0;">
	<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
		<tr>
			<td align="center" style="padding:0;">
				<table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
					<tr>
						<td align="center" style="padding:40px 0 30px 0;background:#70bbd9;">
							<img src="new-bizlx.png" alt="" width="100px" style="height:auto;display:block;" />
							<h3 style="color:#fff">Bizlx.com™   Search Daily Local Hot Deals, Sales, Latest Arrivals, Videos of Shopping Stores Around You</h3>
						</td>
					</tr>
					<tr>
						<td style="padding:36px 30px 42px 30px;">
							<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
								<tr>
									<td style="color:#153643;">
										<h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">Dear {{ $businessName }}</h1>
										<p style="margin:0 0 10px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;"><b>{{$username}}</b> had shown interest in your {{$job_name}}post.</p>
                                        <div>
                                            <card>
                                                <card-title><h4>User Details</h4></card-title>
                                                <card-body>
                                                    <div class="d-flex">
                                                        <span>Name -</span>
                                                        <span>{{$name}}</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <span>Phone No -</span>
                                                        <span>{{$phone}}</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <span>Experience -</span>
                                                        <span>{{$experience}}</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <span>Qualification -</span>
                                                        <span>{{$qualification}}</span>
                                                    </div>
                                                </card-body>
                                            </card>
                                        </div>
										<p style="margin:5px 0 0px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">Thanks!</p>
									</td>
								</tr>

							</table>
						</td>
					</tr>
					<tr>
						<td style="padding:20px;background:#c6c6c6;">
							<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;">
								<tr>
									<td style="padding:0;width:50%;" align="left">
										<p style="margin:0;font-size:18px;line-height:16px;font-family:Arial,sans-serif;color:#000;">
											Follow Bizlx :
										</p>
									</td>
									<td style="padding:0;width:50%;" align="right">
										<table role="presentation" style="border-collapse:collapse;border:0;border-spacing:0;">
											<tr>
												<td style="padding:0 0 0 10px;width:38px;">
													<a href="https://www.facebook.com/bizlx" style="color:#ffffff;" target="_blank"><img src="{{asset('images/facebook -logo.png')}}" alt="facebook" width="38" style="height:auto;display:block;border:0;" /></a>
												</td>
												<td style="padding:0 0 0 10px;width:38px;">
													<a href="https://twitter.com/bizlx_" style="color:#ffffff;" target="_blank"><img src="{{asset('images/twitter-logo.png')}}" alt="twitter" width="38" style="height:auto;display:block;border:0;" /></a>
												</td>
												<td style="padding:0 0 0 10px;width:38px;">
													<a href="https://www.instagram.com/bizlx_/" style="color:#ffffff;" target="_blank"><img src="{{asset('images/instagram-logo.png')}}" alt="instagram" width="38" style="height:auto;display:block;border:0;" /></a>
												</td>
												<td style="padding:0 0 0 10px;width:38px;">
													<a href="https://www.linkedin.com/company/bizlx/" style="color:#ffffff;" target="_blank"><img src="{{asset('images/linkedin-logo.png')}}" alt="linkedin" width="38" style="height:auto;display:block;border:0;" /></a>
												</td>

												<td style="padding:0 0 0 10px;width:38px;">
													<a href="https://play.google.com/store/apps/details?id=com.bizlxapp&pli=1" style="color:#ffffff;"><img src="{{asset('images/playstore-logo.png')}}" alt="playstore" width="110px" style="height:auto;display:block;border:0;" /></a>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
