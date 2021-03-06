<?php
	/* Router Core */
	switch ($__SEGMEN__[2]) {

		case "s-ajaxify" :
		case "s-api" :
			/* Halaman dinamis request ajax -> [DYNAMIC_PAGE] -> [domain]/api/[anything] */
			if(
				$__SEGMEN__[3] 	!= "" 		&& 
				$__SEGMEN__[3] 	!= null 	&& 
				$__XHR_STATUS__	== true 
			) {
				header("Content-type: application/json");
				include(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['api'], $thisReqPathLoginPrefix, $thisReqPath, 3, ""));
			} else {
				require($__ZERO__);
			}
			break;

		case "sec-s-ajaxify" :
		case "sec-s-api" :
			$__FTOKEN__ = $sintaskSess->get("globalSecureToken");
			$__STOKEN__ = $sintaskFW->post("tokenizing");
			/* Halaman dinamis request ajax -> [DYNAMIC_PAGE] -> [domain]/api/[anything] */
			if(
				$__SEGMEN__[3] 	!= "" 			&& 
				$__SEGMEN__[3] 	!= null 		&& 
				$__FTOKEN__ 	== $__STOKEN__ 
			) {
				header("Content-type: application/json");
				include(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['api_sec'], $thisReqPathLoginPrefix, $thisReqPath, 3, ""));
			} else {
				require($__ZERO__);
			}
			break;

		case "s-dl" :
			if(
				$__SEGMEN__[3] 	!= ""		&&
				$__SEGMEN__[3] 	!= null 	&&
				!ctype_space($__SEGMEN__[3])
			) {
				$__LOWER_CASE_SEG3__ = strtolower($__SEGMEN__[3]);
				if($__LOWER_CASE_SEG3__ == "direct") {
					include($__DOC_ROOT__.$requirePath['static']."/static.direct.download.php");
				} else if($__LOWER_CASE_SEG3__ == "ndr") {
					include($__DOC_ROOT__.$requirePath['static']."/static.not.direct.download.php");
				} else {
					require($__ZERO__);
				}
			} else {
				require($__ZERO__);
			}
			break;

		case "s-update" :
			if($__MY_AUTO_UPDATE__["AUTO_UPDATE_PAGE"] == true) {
				if(
					$__SEGMEN__[3] 	!= ""		&&
					$__SEGMEN__[3] 	!= null 	&&
					!ctype_space($__SEGMEN__[3])
				) {
					if($__SEGMEN__[3] == $__MY_AUTO_UPDATE__["AUTO_UPDATE_SECRET_KEY"]) {
						include($__DOC_ROOT__.$requirePath['static']."/static.auto.update.php");
						break;
					}
				}
			}

		default :
			/* Pindahkan GET Param ke SESSION */
			foreach($_GET as $key => $value) {
				$_SESSION['postGET'][$key] = $value;
			}
			/* Pindahkan POST Param ke SESSION */
			foreach($_POST as $key => $value) {
				$_SESSION['postPOST'][$key] = $value;
			}
			/* Pindahkan FILES Param ke SESSION */
			foreach($_FILES as $key => $value) {
				$__TMP_DIR_FILE__ = $__DOC_ROOT__."/protected/data/tmp";
				
				/* Check Directory EXIST */
				if(!is_dir($__TMP_DIR_FILE__)) {
				    mkdir($__TMP_DIR_FILE__, 0755, true);
				}

				/* Pindahkan FILES ke tmp Directory */
				$tmpFile = $__TMP_DIR_FILE__.getRandomPlusDate(5).".tmp";
				move_uploaded_file($_FILES[$key]['tmp_name'], $tmpFile);

				/* Rename tmp_name menjadi Value baru */
				$_FILES[$key]['tmp_name'] = $tmpFile;
				$_SESSION['postFILES'][$key] = $value;
				$_SESSION['postFILES'][$key]['tmp_name'] = $tmpFile;
			}
			
			$__FTOKEN__ = $sintaskSess->get("globalSecureToken");
			$__STOKEN__ = $sintaskFW->post("tokenizing");

			/* Halaman dinamis request Page & SPA -> [DYNAMIC_PAGE] -> [domain]/[anything] */
			if(
				$__XHR_STATUS__ 	== true 				&& 
				$__END_SEGMEN_DOT__ != "jssintasktemplate" 	&& 
				$__END_SEGMEN_DOT__ != "jsd" 				&& 
				$__END_SEGMEN_DOT__ != "latecss" 			&&
				$__END_SEGMEN_DOT__ != "staycontent" 		&&
				$__END_SEGMEN_DOT__ != "stayheader" 		&&
				$__END_SEGMEN_DOT__ != "stayfooter"			&&
				$__FTOKEN__ 		== $__STOKEN__
			) {
				/* [SPA] XHR / AJAX */
				/* GENERAL FIRST */
				if(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['general'], $thisReqPathLoginPrefix, $thisReqPath, 2, "") != $__ZERO__) {
					/* [OTHER] Jika halaman Normal-General */
					header("Content-type: text/javascript");
					?>
						$("html").remove();
						location.assign(document.URL);
					<?php
					die();
				}
				/* SPA SECOND */
				header("Content-type: application/json");
				if(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['template'], $thisReqPathLoginPrefix, $thisReqPath, 2, "") != $__ZERO__) {
					$__META_PATH__ = fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['template'], $thisReqPathLoginPrefix, $thisReqPath, 2, "");

					$normalizingPage = "";
					if(pureUrlPage($__BASE_URL__) == $__BASE_URL__) {
						$normalizingPage = "/";
					}

					echo initialJson(
						$sintaskNewMeta->getTitleMeta($__META_PATH__), 
						pureUrlPage($__BASE_URL__).$normalizingPage.".latecss", 
						pureUrlPage($__BASE_URL__).$normalizingPage.".jssintasktemplate?type=content"
					);
					$_SESSION['404_DETECT'] = false;
				} else {
					/* Tidak menemukan SPA tidak ke $__ZERO__ */
					$__META_PATH__ = fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['template'], "default", $thisReqPath, 2, "");
					echo notFound404Template($sintaskNewMeta->getTitleMeta($__META_PATH__));
					$_SESSION['404_DETECT'] = true;
				}
			} else if(
				$__XHR_STATUS__ 	== true 				&& 
				$__END_SEGMEN_DOT__ == "jssintasktemplate" 	&&
				$__FTOKEN__ 		== $__STOKEN__
			) {
				/* [SPA] jika halaman .jssintasktemplate (JavaSciprt SinTask Template) */
				header("Content-type: text/javascript");
				if(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['template'], $thisReqPathLoginPrefix, $thisReqPath, 2, ".jssintasktemplate") != $__ZERO__) {

					/* Memuat Control */
					$controlRender = fileDynamic($__SEGMEN_PURE__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['control'], $thisReqPathLoginPrefix, $thisReqPath, 2, "");

					/* Custom Template */
					$pathRender = fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['template'], $thisReqPathLoginPrefix, $thisReqPath, 2, ".jssintasktemplate");

					/* HTML Rendering */
					ob_start();
					include($controlRender);
				    include($pathRender);
				    $varRender = ob_get_contents(); 
				    ob_end_clean();

				    /* Tunjukan HTML rendering ke JS SinTask Standard Format */
					if($_GET['type'] == 'content') {
						if($__MY_CORE__["AES_SECURE_SPA_TRANSF"] == true) {
							echo renderHTMLToJSENC($varRender);
						} else {
							echo renderHTMLToJS($varRender);
						}
					}
				} else {
					/* Tidak menemukan SPA tidak ke $__ZERO__ */
					$__SEGMEN__ 	= [];
					$__SEGMEN__ 	= ["System", "GoTo", "404"];
					$pathRender = fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['template'], "default", $thisReqPath, 2, ".jssintasktemplate");

					/* HTML Rendering */
					ob_start();
				    include($pathRender);
				    $varRender = ob_get_contents(); 
				    ob_end_clean();

				    /* Tunjukan HTML rendering ke JS SinTask Standard Format */
					if($_GET['type'] == 'content') {
						if($__MY_CORE__["AES_SECURE_SPA_TRANSF"] == true) {
							echo renderHTMLToJSENC($varRender);
						} else {
							echo renderHTMLToJS($varRender);
						}
					}
				}
			} else if(
				$__XHR_STATUS__ 	== true 				&& 
				$__END_SEGMEN_DOT__ == "latecss"
			) {
				/* [SPA] Jika halaman adalah .latecss (CSS late load) */
				header("Content-type: text/css");
				if(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['latecss'], $thisReqPathLoginPrefix, $thisReqPath, 2, ".latecss") != $__ZERO__) {
					include(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['latecss'], $thisReqPathLoginPrefix, $thisReqPath, 2, ".latecss"));
				} else {
					/* Tidak menemukan SPA tidak ke $__ZERO__ */
					$__SEGMEN__ 	= [];
					$__SEGMEN__ 	= ["System", "GoTo", "404"];
					include(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['latecss'], "default", $thisReqPath, 2, ".latecss"));
				}

				/* Menghapus postGET & postPOST - karena terakhir di load */
				unset($_SESSION['postGET']);
				unset($_SESSION['postPOST']);
				foreach($_SESSION['postFILES'] as $key => $value) {
					unlink($_SESSION['postFILES'][$key]['tmp_name']);
				}
				unset($_SESSION['postFILES']);
			} else if(
				$__XHR_STATUS__ 	== true 				&& 
				$__END_SEGMEN_DOT__ == "staycontent"		&&
				$__FTOKEN__ 		== $__STOKEN__
			) {
				header("Content-type: text/javascript");
				$pathRender = $__DOC_ROOT__.$requirePath['stay']."/stay_content.php";

				/* HTML Rendering */
				ob_start();
			    include($pathRender);
			    $varRender = ob_get_contents(); 
			    ob_end_clean();

			    /* Tunjukan HTML rendering ke JS SinTask Standard Format */
			    if($__MY_CORE__["AES_SECURE_SPA_TRANSF"] == true) {
					echo renderHTMLToJSStayENC("content", $varRender);
			    } else {
			    	echo renderHTMLToJSStay("content", $varRender);
			    }
			} else if(
				$__XHR_STATUS__ 	== true 				&& 
				$__END_SEGMEN_DOT__ == "stayheader" 		&& 
				$__FTOKEN__ 		== $__STOKEN__
			) {
				header("Content-type: text/javascript");
				$pathRender = $__DOC_ROOT__.$requirePath['stay']."/stay_header.php";

				/* HTML Rendering */
				ob_start();
			    include($pathRender);
			    $varRender = ob_get_contents(); 
			    ob_end_clean();

			    /* Tunjukan HTML rendering ke JS SinTask Standard Format */
			    if($__MY_CORE__["AES_SECURE_SPA_TRANSF"] == true) {
					echo renderHTMLToJSStayENC("header", $varRender);
				} else {
					echo renderHTMLToJSStay("header", $varRender);
				}
			} else if(
				$__XHR_STATUS__ 	== true 				&& 
				$__END_SEGMEN_DOT__ == "stayfooter" 		&&
				$__FTOKEN__ 		== $__STOKEN__
			) {
				header("Content-type: text/javascript");
				$pathRender = $__DOC_ROOT__.$requirePath['stay']."/stay_footer.php";

				/* HTML Rendering */
				ob_start();
			    include($pathRender);
			    $varRender = ob_get_contents(); 
			    ob_end_clean();

			    /* Tunjukan HTML rendering ke JS SinTask Standard Format */
			    if($__MY_CORE__["AES_SECURE_SPA_TRANSF"] == true) {
					echo renderHTMLToJSStayENC("footer", $varRender);
				} else {
					echo renderHTMLToJSStay("footer", $varRender);
				}
			} else if($__END_SEGMEN_DOT__ == "jsd") {
				/* [OTHER] Jika halaman adalah .jsd (JavaSciprt Dynamic) */
				header("Content-type: text/javascript");
				if(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['jsd'], $thisReqPathLoginPrefix, $thisReqPath, 2, ".jsd") != $__ZERO__) {
					include(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['jsd'], $thisReqPathLoginPrefix, $thisReqPath, 2, ".jsd"));
				} else {
					/* Tidak menemukan SPA tidak ke $__ZERO__ */
					$__SEGMEN__ 	= [];
					$__SEGMEN__ 	= ["System", "GoTo", "404"];
					include(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['jsd'], "default", $thisReqPath, 2, ".jsd"));
				}
			} else {
				if(
					$__XHR_STATUS__ == true 		&&
					$__FTOKEN__ 	!= $__STOKEN__
				) {
					header("Content-type: application/json");
					echo invalidToken($__ERROR_INVALID_MSG__, $__FTOKEN__, $__STOKEN__);
					die();
				}

				if(fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['general'], $thisReqPathLoginPrefix, $thisReqPath, 2, "") != $__ZERO__) {
					/* General Blocker */
					$generalBlocker = $__DOC_ROOT__.$requirePath['static']."/static.general.blocker.php";

					/* Load Control */
					$controlState = fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['control'], $thisReqPathLoginPrefix, $thisReqPath, 2, "");

					/* [OTHER] Jika halaman adalah General/Normal */
					$generalState = fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['general'], $thisReqPathLoginPrefix, $thisReqPath, 2, "");

					$explodeGeneralPath = explode("/", $generalState);
					$endGeneralPath = end($explodeGeneralPath);
					$explodeGeneralPathFileName = explode(".", $endGeneralPath);
					array_pop($explodeGeneralPathFileName);
					$expectFileName = implode(".", $explodeGeneralPathFileName);

					if(in_array($expectFileName, $__HTML_META_GENERAL__["EXPECT_META_LIST"])) {
						$__HTML_META_GENERAL__["DEFAULT_META"] = false;
					}

					include($generalBlocker);
					include($controlState);

					if($__HTML_META_GENERAL__["DEFAULT_META"] == true) {
						$thisCoreGet = "doctype";
						include($__HTML_CORE_REQ__);

						$thisCoreGet = "headstart";
						include($__HTML_CORE_REQ__);

						$thisCoreGet = "scripttop";
						include($__HTML_CORE_REQ__);

						$thisCoreGet = "headend";
						include($__HTML_CORE_REQ__);
					}

					include($generalState);

					if($__HTML_META_GENERAL__["DEFAULT_META"] == true) {
						$thisCoreGet = "scriptend_general";
						include($__HTML_CORE_REQ__);

						$thisCoreGet = "foothtml";
						include($__HTML_CORE_REQ__);
					}

					/* Menghapus postGET & postPOST */
					unset($_SESSION['postGET']);
					unset($_SESSION['postPOST']);
					foreach($_SESSION['postFILES'] as $key => $value) {
						unlink($_SESSION['postFILES'][$key]['tmp_name']);
					}
				} else {
					/* [SPA] Jika halaman adalah SPA */
					$thisCoreGet = "doctype";
					include($__HTML_CORE_REQ__);

					$thisCoreGet = "headstart";
					include($__HTML_CORE_REQ__);

					/* Jika Javascript tidak aktif */
					if($__SEGMEN__[2] == "jsdisabled") {
						?><title>Javascript</title><?php
					}

					$__META_PATH__ = fileDynamic($__SEGMEN__, $__FILE_EXTENSION__, $__ZERO__, $requirePath['template'], $thisReqPathLoginPrefix, $thisReqPath, 2, "");
					echo $sintaskNewMeta->readingMeta($__META_PATH__);
				
					$thisCoreGet = "scripttop";
					include($__HTML_CORE_REQ__);

					$thisCoreGet = "headend";
					include($__HTML_CORE_REQ__);

					$thisCoreGet = "bodystart";
					include($__HTML_CORE_REQ__);

					$thisCoreGet = "content";
					include($__HTML_CORE_REQ__);

					$thisCoreGet = "scriptend";
					include($__HTML_CORE_REQ__);

					$thisCoreGet = "bodyend";
					include($__HTML_CORE_REQ__);

					$thisCoreGet = "foothtml";
					include($__HTML_CORE_REQ__);
				}
			}
			break;
	}
?>