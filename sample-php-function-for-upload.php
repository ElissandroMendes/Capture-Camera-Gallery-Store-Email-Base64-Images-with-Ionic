<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}

$postdata = file_get_contents("php://input");	
$json_array = new stdClass();

if (isset($postdata)) {
	$request = json_decode(trim($postdata));	
	if($request === null) {
		 //error validation
		$json_array->error->code = 1;
		$json_array->error->message = 'Invalid JSON Data';		
		echo json_encode($json_array); 
		exit;
	}
	if($request->api_key != 'XYZ2017'){
		header('HTTP/1.1 403 Unauthorized', true, 403);
		$json_array->error->code = 1;
		$json_array->error->message = 'Invalid API Key';		
		echo json_encode($json_array); 
		exit;
	}
}else{			
	//error validation
	$json_array->error->code = 1;
	$json_array->error->message = 'Please fill all required fields';	
	echo json_encode($json_array); 
	exit;	
}	


function updateProfile($postdata){
	$json_array = new stdClass();
	$result = updateProfileDetails($postdata);
	if($result){			
			$json_array->message = 'Successfully saved';
			$json_array->status = 'Success';			
	}else{
		$json_array->status = 'Error';
		$json_array->message = 'Unable to save';
	}	
		
	echo json_encode($json_array); 
	exit;		
}

?>

/** IONIC Request 

$scope.updateProfile = function(postdata) {
		jQuery('.account_form .required').each(function(index){
			if(jQuery(this).val() == ''){
				jQuery(this).parent().addClass('error');         
			}else{
				jQuery(this).parent().removeClass('error');
			}
		});
		if(jQuery('.account_form .required').parent().hasClass('error')) return false;
		
		if ($scope.profile_image != null && $scope.profile_image.length > 0) {
		  var savedImages = $scope.profile_image;	  
		}else{
			var savedImages = '';	
		}
		var current_user = AuthService.current_user();
		$scope.ajaxDisplayProject = true;
		 $http.post(WEB_SERVICES.api_url, {api_key:WEB_SERVICES.api_key, action : 'update-profile',name:postdata.name,companyname:postdata.companyname,city:postdata.city,stategroup:postdata.stategroup,zipcode:postdata.zipcode,address:postdata.address,phonenumber:postdata.phonenumber,fax:postdata.fax,website:postdata.website,profile_image:savedImages,user_id:current_user.id}).then(function (response){																															
			var alertPopup = $ionicPopup.alert({
				title: response.data.status,
				template: response.data.message
			});
			$scope.ajaxDisplayProject = false;
			if(response.data.status =='Success'){
				$state.go('main.settings', {}, {reload: true});
			}
		  });
	  
	  }

*/




function updateProfileDetails($postdata){
	$profile_image = $postdata->profile_image;
	
	$upload_path =JPATH_ROOT.'/images/avatars/';
	
		
	if(!empty($profile_image)){
		$avatar_image ='';
			$img_base64 = str_replace('data:image/png;base64,', '', $profile_image);
			$img_base64 = str_replace('data:image/jpg;base64,', '', $img_base64);
			$img_base64 = str_replace('data:image/jpeg;base64,', '', $img_base64);
			$img_base64 = str_replace('data:image/gif;base64,', '', $img_base64);			
			$img_base64 = str_replace(' ', '+', $img_base64);
			
			$decoded=base64_decode($img_base64);
			$mimetype = getImageMimeType($decoded);

			$image_name = uniqid() . '.'.$mimetype;
			file_put_contents($upload_path.$image_name,$decoded);	
			$avatar_image = $image_name;
	}
		
}
