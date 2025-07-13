<?php
require_once 'database.php';
require_once 'site_config_helper.php';
$db = Database::getInstance();

// Get recent articles for homepage
$recentArticles = $db->getArticles(3);

// 获取网站配置
$location = SiteConfigHelper::getLocation();
$educationInfo = SiteConfigHelper::getEducationInfo();
$personalTags = SiteConfigHelper::getPersonalTags();
$timelineEvents = SiteConfigHelper::getTimelineEvents();
$welcomeName = SiteConfigHelper::getWelcomeName();
$welcomeDesc1 = SiteConfigHelper::getWelcomeDescription1();
$welcomeDesc2 = SiteConfigHelper::getWelcomeDescription2();
$email = SiteConfigHelper::getEmail();
$githubUrl = SiteConfigHelper::getGithubUrl();
$wechatQr = SiteConfigHelper::getWechatQr();
$googleScholar = SiteConfigHelper::getGoogleScholar();
$orcid = SiteConfigHelper::getOrcid();
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo htmlspecialchars(SiteConfigHelper::getSiteTitle()); ?></title>
	<link rel="icon" href="./static/img/icon/icon-nav.png">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="stylesheet" href="./static/css/root.css">
	<link rel="stylesheet" href="./static/css/style.css">
</head>

<body>
	<div id="loading">
		<div id="loading-center"></div>
	</div>
	<div class="filter"></div>

	<div class="main">
		<!-- 左侧边栏 -->
		<div class="left">
			<div class="logo">
			</div>
			<div class="left-div left-des">
				                <div class="left-des-item">
                    <svg t="1705773709627" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="1478">
                        <path
                            d="M512 249.976471c-99.388235 0-180.705882 81.317647-180.705882 180.705882s81.317647 180.705882 180.705882 180.705882 180.705882-81.317647 180.705882-180.705882-81.317647-180.705882-180.705882-180.705882z m0 301.17647c-66.258824 0-120.470588-54.211765-120.470588-120.470588s54.211765-120.470588 120.470588-120.470588 120.470588 54.211765 120.470588 120.470588-54.211765 120.470588-120.470588 120.470588z"
                            p-id="1479"></path>
                        <path
                            d="M512 39.152941c-216.847059 0-391.529412 174.682353-391.529412 391.529412 0 349.364706 391.529412 572.235294 391.529412 572.235294s391.529412-222.870588 391.529412-572.235294c0-216.847059-174.682353-391.529412-391.529412-391.529412z m0 891.482353C424.658824 873.411765 180.705882 686.682353 180.705882 430.682353c0-183.717647 147.576471-331.294118 331.294118-331.294118s331.294118 147.576471 331.294118 331.294118c0 256-243.952941 442.729412-331.294118 499.952941z"
                            p-id="1480"></path>
                    </svg>
                    <?php echo htmlspecialchars($location); ?>
                </div>
				                <div class="left-des-item">
                    <svg t="1705773906032" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="2474">
                        <path
                            d="M729.6 234.666667H294.4V157.866667a51.2 51.2 0 0 1 51.2-51.2h332.8a51.2 51.2 0 0 1 51.2 51.2v76.8z m179.2 51.2a51.2 51.2 0 0 1 51.2 51.2v512a51.2 51.2 0 0 1-51.2 51.2H115.2a51.2 51.2 0 0 1-51.2-51.2v-512a51.2 51.2 0 0 1 51.2-51.2h793.557333z m-768 172.032c0 16.384 13.312 29.696 29.696 29.696h683.008a29.696 29.696 0 1 0 0-59.392H170.410667a29.696 29.696 0 0 0-29.696 29.696z m252.416 118.784c0 16.384 13.312 29.696 29.696 29.696h178.176a29.696 29.696 0 1 0 0-59.392H422.912a29.738667 29.738667 0 0 0-29.696 29.696z"
                            p-id="2475"></path>
                    </svg>
                    <?php echo htmlspecialchars($educationInfo); ?>
                </div>
			</div>
			            <div class="left-div left-tag">
                <?php foreach ($personalTags as $tag): ?>
                <div class="left-tag-item"><?php echo htmlspecialchars($tag); ?>
                </div>
                <?php endforeach; ?>
            </div>
			            <div class="left-div left-time">
                <ul id="line">
                    <?php foreach ($timelineEvents as $event): ?>
                    <li>
                        <div class="focus"></div>
                        <div><?php echo htmlspecialchars($event['event']); ?></div>
                        <div><?php echo htmlspecialchars($event['date']); ?></div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
		</div>
		<!-- 右侧主内容区 -->
		<div class="right">
			<header>
				<div class="index-logo">
				</div>
				                <div class="welcome">
                    Hello I' m <span class="gradientText"><?php echo htmlspecialchars($welcomeName); ?></span>
                </div>
                <div class="description"><?php echo htmlspecialchars($welcomeDesc1); ?>
                </div>
                <div class="description"><?php echo htmlspecialchars($welcomeDesc2); ?>
                </div>

				<div class="iconContainer">
					                    <a class="iconItem" onclick="" href="<?php echo htmlspecialchars($githubUrl); ?>">
						<svg t="1704870335945" class="icon" viewBox="0 0 1024 1024" version="1.1"
							xmlns="http://www.w3.org/2000/svg" p-id="2487">
							<path
								d="M511.6 76.3C264.3 76.2 64 276.4 64 523.5 64 718.9 189.3 885 363.8 946c23.5 5.9 19.9-10.8 19.9-22.2v-77.5c-135.7 15.9-141.2-73.9-150.3-88.9C215 726 171.5 718 184.5 703c30.9-15.9 62.4 4 98.9 57.9 26.4 39.1 77.9 32.5 104 26 5.7-23.5 17.9-44.5 34.7-60.8-140.6-25.2-199.2-111-199.2-213 0-49.5 16.3-95 48.3-131.7-20.4-60.5 1.9-112.3 4.9-120 58.1-5.2 118.5 41.6 123.2 45.3 33-8.9 70.7-13.6 112.9-13.6 42.4 0 80.2 4.9 113.5 13.9 11.3-8.6 67.3-48.8 121.3-43.9 2.9 7.7 24.7 58.3 5.5 118 32.4 36.8 48.9 82.7 48.9 132.3 0 102.2-59 188.1-200 212.9 23.5 23.2 38.1 55.4 38.1 91v112.5c0.8 9 0 17.9 15 17.9 177.1-59.7 304.6-227 304.6-424.1 0-247.2-200.4-447.3-447.5-447.3z"
								p-id="2488"></path>
						</svg>
						<div class="iconTip">Github</div>
					                    </a><a class="iconItem" onclick="" href="mailto:<?php echo htmlspecialchars($email); ?>">
						<svg t="1704870588438" class="icon" viewBox="0 0 1024 1024" version="1.1"
							xmlns="http://www.w3.org/2000/svg" p-id="3174">
							<path
								d="M926.47619 355.644952V780.190476a73.142857 73.142857 0 0 1-73.142857 73.142857H170.666667a73.142857 73.142857 0 0 1-73.142857-73.142857V355.644952l304.103619 257.828572a170.666667 170.666667 0 0 0 220.745142 0L926.47619 355.644952zM853.333333 170.666667a74.044952 74.044952 0 0 1 26.087619 4.778666 72.704 72.704 0 0 1 30.622477 22.186667 73.508571 73.508571 0 0 1 10.678857 17.67619c3.169524 7.509333 5.12 15.652571 5.607619 24.210286L926.47619 243.809524v24.380952L559.469714 581.241905a73.142857 73.142857 0 0 1-91.306666 2.901333l-3.632762-2.925714L97.52381 268.190476v-24.380952a72.899048 72.899048 0 0 1 40.155428-65.292191A72.97219 72.97219 0 0 1 170.666667 170.666667h682.666666z"
								p-id="3175"></path>
						</svg>
						<div class="iconTip">Mail</div>
					                    </a><a class="iconItem" onclick="pop('<?php echo htmlspecialchars($wechatQr); ?>')" href="javascript:void(0)">
						<svg t="1742911204889" class="icon" viewBox="0 0 1024 1024" version="1.1"
							xmlns="http://www.w3.org/2000/svg" p-id="4614" width="200" height="200">
							<path
								d="M690.1 377.4c5.9 0 11.8 0.2 17.6 0.5-24.4-128.7-158.3-227.1-319.9-227.1C209 150.8 64 271.4 64 420.2c0 81.1 43.6 154.2 111.9 203.6 5.5 3.9 9.1 10.3 9.1 17.6 0 2.4-0.5 4.6-1.1 6.9-5.5 20.3-14.2 52.8-14.6 54.3-0.7 2.6-1.7 5.2-1.7 7.9 0 5.9 4.8 10.8 10.8 10.8 2.3 0 4.2-0.9 6.2-2l70.9-40.9c5.3-3.1 11-5 17.2-5 3.2 0 6.4 0.5 9.5 1.4 33.1 9.5 68.8 14.8 105.7 14.8 6 0 11.9-0.1 17.8-0.4-7.1-21-10.9-43.1-10.9-66 0-135.8 132.2-245.8 295.3-245.8z m-194.3-86.5c23.8 0 43.2 19.3 43.2 43.1s-19.3 43.1-43.2 43.1c-23.8 0-43.2-19.3-43.2-43.1s19.4-43.1 43.2-43.1z m-215.9 86.2c-23.8 0-43.2-19.3-43.2-43.1s19.3-43.1 43.2-43.1 43.2 19.3 43.2 43.1-19.4 43.1-43.2 43.1z"
								p-id="4615"></path>
							<path
								d="M866.7 792.7c56.9-41.2 93.2-102 93.2-169.7 0-124-120.8-224.5-269.9-224.5-149 0-269.9 100.5-269.9 224.5S540.9 847.5 690 847.5c30.8 0 60.6-4.4 88.1-12.3 2.6-0.8 5.2-1.2 7.9-1.2 5.2 0 9.9 1.6 14.3 4.1l59.1 34c1.7 1 3.3 1.7 5.2 1.7 2.4 0 4.7-0.9 6.4-2.6 1.7-1.7 2.6-4 2.6-6.4 0-2.2-0.9-4.4-1.4-6.6-0.3-1.2-7.6-28.3-12.2-45.3-0.5-1.9-0.9-3.8-0.9-5.7 0.1-5.9 3.1-11.2 7.6-14.5zM600.2 587.2c-19.9 0-36-16.1-36-35.9 0-19.8 16.1-35.9 36-35.9s36 16.1 36 35.9c0 19.8-16.2 35.9-36 35.9z m179.9 0c-19.9 0-36-16.1-36-35.9 0-19.8 16.1-35.9 36-35.9s36 16.1 36 35.9c-0.1 19.8-16.2 35.9-36 35.9z"
								p-id="4616"></path>
						</svg>
						<div class="iconTip">Wechat</div>
					                    </a><a class="iconItem" onclick="" href="<?php echo htmlspecialchars($googleScholar); ?>">
						<svg t="1742911302568" class="icon" viewBox="0 0 1024 1024" version="1.1"
							xmlns="http://www.w3.org/2000/svg" p-id="9006" width="200" height="200">
							<path
								d="M466.346667 87.253333c-23.338667 15.616-137.386667 91.306667-253.354667 168.32C96.938667 332.544 2.133333 396.202667 2.133333 396.970667c0 0.810667 5.802667 4.693333 13.013334 8.533333 7.125333 4.096 121.6 67.541333 254.506666 141.226667l241.322667 134.101333 6.144-3.157333c3.498667-1.706667 92.544-52.565333 198.058667-112.725334l191.701333-109.568 1.152 339.072h113.834667V397.610667l-147.626667-98.986667c-198.997333-133.290667-358.058667-238.336-361.984-239.189333-1.92-0.341333-22.485333 12.245333-45.909333 27.818666M228.48 709.674667l0.597333 85.546666 141.226667 84.778667 141.226667 84.565333 142.378666-85.333333 142.165334-85.546667V709.12c0-46.592-0.554667-84.608-1.152-84.608s-56.234667 33.365333-123.733334 74.282667l-141.056 85.162666-18.389333 10.922667-56.32-33.749333a25775.786667 25775.786667 0 0 1-140.202667-84.437334l-85.546666-51.626666c-1.024-0.341333-1.365333 37.674667-1.152 84.608"
								p-id="9007"></path>
						</svg>
						<div class="iconTip">Scholar</div>
					                    </a><a class="iconItem" onclick="" href="<?php echo htmlspecialchars($orcid); ?>">
						<svg t="1742918989426" class="icon" viewBox="0 0 1024 1024" version="1.1"
							xmlns="http://www.w3.org/2000/svg" p-id="4627" width="200" height="200">
							<path
								d="M589.5 376.38h-91.84V684h94.94c135.24 0 166.24-102.68 166.24-153.82 0-83.28-53.08-153.8-169.34-153.8zM512 16C238 16 16 238 16 512s222 496 496 496 496-222 496-496S786 16 512 16z m-161.58 721.52h-59.68v-415h59.68z m-29.84-462.28a39.14 39.14 0 1 1 39.14-39.14 39.28 39.28 0 0 1-39.14 39.14zM600 738h-162V322.52h161.2c153.46 0 220.88 109.66 220.88 207.7C820 636.78 736.76 738 600 738z"
								p-id="4628"></path>
						</svg>
						<div class="iconTip">Orcid</div>
					</a><a class="switch" href="javascript:void(0)">
						<div class="onoffswitch">
							<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"
								checked>
							<label class="onoffswitch-label" for="myonoffswitch">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</a>
				</div>
			</header>

			<main>
				<!-- 站点导航部分 -->
				<section>
				<div class="title">
					<svg t="1743081402902" class="icon" viewBox="0 0 1024 1024" version="1.1"
						xmlns="http://www.w3.org/2000/svg" p-id="3874" width="200" height="200">
						<path
							d="M907.636364 643.397818A54.853818 54.853818 0 0 1 852.852364 698.181818H171.147636A54.853818 54.853818 0 0 1 116.363636 643.397818V217.716364A54.877091 54.877091 0 0 1 171.147636 162.909091h681.704728A54.877091 54.877091 0 0 1 907.636364 217.716364v425.681454zM852.852364 93.090909H171.147636A124.765091 124.765091 0 0 0 46.545455 217.716364v425.681454A124.741818 124.741818 0 0 0 171.147636 768H477.090909v93.090909H302.545455a34.909091 34.909091 0 0 0 0 69.818182h418.90909a34.909091 34.909091 0 1 0 0-69.818182h-174.545454v-93.090909h305.943273A124.741818 124.741818 0 0 0 977.454545 643.397818V217.716364A124.765091 124.765091 0 0 0 852.852364 93.090909z"
							fill="#ffffff" p-id="3875"></path>
					</svg>
					site
				</div>
				<div class="projectList">
					<a class="projectItem a" target="_blank" href="about.php">
						<div class="projectItemLeft">
							<h1>About Me</h1>
							<p>关于我</p>
						</div>
						<div class="projectItemRight">
							<img src="./static/img/icon/i1.png" alt="">
						</div>
					</a>
					<a class="projectItem a" target="_blank" href="blog.php">
						<div class="projectItemLeft">
							<h1>My Blog</h1>
							<p>我的博客</p>
						</div>
						<div class="projectItemRight">
							<img src="./static/img/icon/i2.png" alt="">
						</div>
					</a>
					<a class="projectItem a" target="_blank" href="articles.php">
						<div class="projectItemLeft">
							<h1>推荐阅读</h1>
							<p>文章 & 工具分享</p>
						</div>
						<div class="projectItemRight">
							<img src="./static/img/icon/i3.png" alt="">
						</div>
					</a>
					<a class="projectItem a" target="_blank" href="https://yizhexie.github.io/">
						<div class="projectItemLeft">
							<h1>Academic</h1>
							<p>我的学术主页</p>
						</div>
						<div class="projectItemRight">
							<img src="./static/img/icon/i4.png" alt="">
						</div>
					</a>
				</div>
				</section>

				<!-- 项目展示部分 -->
				<section>
				<div class="title">
					<svg t="1743081455775" class="icon" viewBox="0 0 1024 1024" version="1.1"
						xmlns="http://www.w3.org/2000/svg" p-id="5058" width="200" height="200">
						<path
							d="M134 366.7c18.3 0 33.1-14.8 33.1-33.1v-120c0-6.4 5.2-11.6 11.6-11.6h69.1c4.2 0 8.1 2.3 10.1 6l26.8 48c13.7 24.6 39.8 39.9 68 39.9h492.6c6.4 0 11.6 5.2 11.6 11.6v26.1c0 18.3 14.8 33.1 33.1 33.1s33.1-14.8 33.1-33.1v-26.1c0-42.9-34.9-77.8-77.8-77.8H352.7c-4.2 0-8.1-2.3-10.1-6l-26.8-48c-13.7-24.6-39.8-39.9-68-39.9h-69.1c-42.9 0-77.8 34.9-77.8 77.8v120c0 18.3 14.8 33.1 33.1 33.1zM941.2 407c-14.5-16.8-35.5-26.4-57.7-26.4H140.4c-22.2 0-43.2 9.6-57.7 26.4-14.5 16.8-20.9 39-17.6 60.9l53.3 355.5c5.6 37.6 37.3 64.8 75.2 64.8h636.5c37.4 0 69.7-27.9 75.2-64.8l53.3-355.5c3.5-21.9-2.9-44.1-17.4-60.9z m-47.9 51.1L840 813.6c-0.7 4.8-4.9 8.4-9.8 8.4H193.8c-4.9 0-9-3.5-9.8-8.4l-53.3-355.5c-0.6-3.9 1.2-6.6 2.3-7.9 1.1-1.3 3.5-3.4 7.5-3.4h743.1c3.9 0 6.4 2.1 7.5 3.4 1 1.3 2.8 4 2.2 7.9z"
							fill="#ffffff" p-id="5059"></path>
						<path
							d="M656.2 527.7H367.8c-18.3 0-33.1 14.8-33.1 33.1s14.8 33.1 33.1 33.1h288.3c18.3 0 33.1-14.8 33.1-33.1s-14.7-33.1-33-33.1z"
							fill="#ffffff" p-id="5060"></path>
					</svg>
					project
				</div>
				<div class="projectList">
					<a class="projectItem a" target="_blank" href="https://github.com/YizheXie/YizheXie.github.io">
						<div class="projectItemLeft">
							<h1>同款学术主页</h1>
							<p>风格简洁易上手 ~</p>
						</div>
						<div class="projectItemRight">
							<img src="./static/img/icon/i5.png" alt="">
						</div>
					</a>
					<a class="projectItem a" target="_blank" href="https://anonymous.4open.science/r/AgentXposed-F814">
						<div class="projectItemLeft">
							<h1>AgentXposed</h1>
							<p>一种基于心理学方法的恶意agent检测框架</p>
						</div>
						<div class="projectItemRight">
							<img src="./static/img/icon/i6.png" alt="">
						</div>
					</a>
				</div>
				</section>

				<!-- 技能展示部分 -->
				<section>
				<div class="title">
					<svg t="1705257823317" class="icon" viewBox="0 0 1024 1024" version="1.1"
						xmlns="http://www.w3.org/2000/svg" p-id="7833">
						<path
							d="M395.765333 586.570667h-171.733333c-22.421333 0-37.888-22.442667-29.909333-43.381334L364.768 95.274667A32 32 0 0 1 394.666667 74.666667h287.957333c22.72 0 38.208 23.018667 29.632 44.064l-99.36 243.882666h187.050667c27.509333 0 42.186667 32.426667 24.042666 53.098667l-458.602666 522.56c-22.293333 25.408-63.626667 3.392-54.976-29.28l85.354666-322.421333zM416.714667 138.666667L270.453333 522.581333h166.869334a32 32 0 0 1 30.933333 40.181334l-61.130667 230.954666 322.176-367.114666H565.312c-22.72 0-38.208-23.018667-29.632-44.064l99.36-243.882667H416.714667z"
							p-id="7834"></path>
					</svg>
					skills
				</div>
				<div class="projectList">
					<a class="projectItem a" target="_blank">
						<div class="projectItemLeft">
							<h1>Cooking</h1>
							<p>仍在学习 ing</p>
						</div>
						<div class="projectItemRight">
							<img src="./static/img/icon/i8.png" alt="">
						</div>
					</a>
				</div>
				</section>
			</main>
		</div>
	</div>

	    <!-- 尾注 -->
    <footer>
        <?php echo htmlspecialchars(SiteConfigHelper::getFooterText()); ?>
    </footer>

	<div class="tc">
		<div onclick="" class="tc-main">
			<img class="tc-img" src="" alt="" srcset="">
		</div>
	</div>
	<script src="./static/js/script.js"></script>
	<script src="./static/js/quotes.js"></script>
</body>

</html>