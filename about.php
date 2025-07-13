<?php
require_once 'database.php';
require_once 'site_config_helper.php';
$db = Database::getInstance();

// 获取网站配置
$location = SiteConfigHelper::getLocation();
$educationInfo = SiteConfigHelper::getEducationInfo();
$email = SiteConfigHelper::getEmail();
$githubUrl = SiteConfigHelper::getGithubUrl();
$wechatQr = SiteConfigHelper::getWechatQr();
$googleScholar = SiteConfigHelper::getGoogleScholar();
$orcid = SiteConfigHelper::getOrcid();

// 获取关于我信息
$aboutInfo = [];
$allAboutInfo = $db->getAllAboutInfo();
foreach ($allAboutInfo as $info) {
    if ($info['is_active']) {
        $aboutInfo[$info['section_key']] = $info['content'];
    }
}

// 处理特殊格式的内容
function processAboutContent($content, $type) {
    switch ($type) {
        case 'json':
            return json_decode($content, true);
        case 'html':
            return $content;
        default:
            return $content;
    }
}

// 获取特定信息，如果不存在则返回默认值
function getAboutInfo($key, $default = '') {
    global $aboutInfo;
    return isset($aboutInfo[$key]) ? $aboutInfo[$key] : $default;
}

// 处理技能标签
$skills = [];
$skillsContent = getAboutInfo('skills', '');
if ($skillsContent) {
    $skills = array_map('trim', explode(',', $skillsContent));
}

// 处理教育经历
$education = [];
$educationContent = getAboutInfo('education', '');
if ($educationContent) {
    $parts = explode('|', $educationContent);
    if (count($parts) >= 3) {
        $education = [
            'period' => $parts[0],
            'school' => $parts[1],
            'description' => $parts[2]
        ];
    }
}

// 处理科研项目
$publications = [];
$publicationsContent = getAboutInfo('publications', '');
if ($publicationsContent) {
    $pubItems = explode(';', $publicationsContent);
    foreach ($pubItems as $item) {
        $parts = explode('|', $item);
        if (count($parts) >= 4) {
            $publications[] = [
                'title' => $parts[0],
                'authors' => $parts[1],
                'venue' => $parts[2],
                'url' => $parts[3]
            ];
        }
    }
}

// 处理荣誉奖项
$awards = [];
$awardsContent = getAboutInfo('awards', '');
if ($awardsContent) {
    $parts = explode('|', $awardsContent);
    if (count($parts) >= 3) {
        $awards = [
            'period' => $parts[0],
            'title' => $parts[1],
            'description' => $parts[2]
        ];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars(SiteConfigHelper::getSiteTitle()); ?> - 关于我</title>
    <link rel="icon" href="./static/img/icon/icon-nav.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./static/css/style.css">
    <link rel="stylesheet" href="./static/css/root.css">
    <link rel="stylesheet" href="./static/css/blog.css">
    <link rel="stylesheet" href="./static/css/about.css">
</head>

<body>
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    <div class="filter"></div>

    <!-- 顶部导航栏 -->
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <img src="./static/img/icon/icon-nav.png" alt="Logo">
                    <span><?php echo htmlspecialchars(SiteConfigHelper::getWelcomeName()); ?></span>
                </a>
            </div>
            <div class="nav-links">
                <a href="index.php">首页</a>
                <a href="blog.php">博客</a>
                <a href="articles.php">推荐阅读</a>
                <a href="about.php" class="active">关于我</a>
            </div>
            <div class="nav-admin">
                <a href="admin/login.php" class="admin-login-btn">
<svg t="1752382187165" class="icon" viewBox="0 0 1164 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2806" width="200" height="200"><path d="M1047.326501 0 116.366581 0c-64.006392 0-116.366581 52.367007-116.366581 116.366581l0 640.029831c0 64.006392 51.344214 126.642242 114.116436 139.195323l254.375464 50.873729c0 0-218.939092 77.575448-77.568629 77.575448l581.846541 0c141.390919 0-77.568629-77.575448-77.568629-77.575448l254.395919-50.873729c62.751766-12.553081 114.09598-75.18893 114.09598-139.195323L1163.693082 116.366581C1163.693082 52.367007 1111.326075 0 1047.326501 0zM1047.326501 750.580129 116.366581 750.580129 116.366581 104.734014l930.953102 0L1047.319683 750.580129z" fill="#040000" p-id="2807"></path></svg>
                    后台管理
                </a>
            </div>
            <div class="nav-toggle">
                <div class="toggle-line"></div>
                <div class="toggle-line"></div>
                <div class="toggle-line"></div>
            </div>
        </div>
    </nav>

    <div class="main">
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
            <!-- 随机名言区域 -->
            <div class="quote-box">
                <div class="quote-content"></div>
                <div class="quote-refresh">
                    <button>
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="right">
            <header>
                <div class="index-logo">
                </div>
                <div class="welcome">
                    关于我
                </div>
                <div class="description">✨ 技术探索者 & 终身学习者 <span class="purpleText">Tech Explorer & Lifelong Learner</span>
                </div>

                <div class="iconContainer">
                    <a class="iconItem" href="index.php">
                        <svg t="1742999202713" class="icon" viewBox="0 0 1029 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="1490" width="200" height="200">
                            <path
                                d="M1001.423238 494.592q21.504 20.48 22.528 45.056t-16.384 40.96q-19.456 17.408-45.056 16.384t-40.96-14.336q-5.12-4.096-31.232-28.672t-62.464-58.88-77.824-73.728-78.336-74.24-63.488-60.416-33.792-31.744q-32.768-29.696-64.512-28.672t-62.464 28.672q-10.24 9.216-38.4 35.328t-65.024 60.928-77.824 72.704-75.776 70.656-59.904 55.808-30.208 27.136q-15.36 12.288-40.96 13.312t-44.032-15.36q-20.48-18.432-19.456-44.544t17.408-41.472q6.144-6.144 37.888-35.84t75.776-70.656 94.72-88.064 94.208-88.064 74.752-70.144 36.352-34.304q38.912-37.888 83.968-38.4t76.8 30.208q6.144 5.12 25.6 24.064t47.616 46.08 62.976 60.928 70.656 68.096 70.144 68.096 62.976 60.928 48.128 46.592zM447.439238 346.112q25.6-23.552 61.44-25.088t64.512 25.088q3.072 3.072 18.432 17.408l38.912 35.84q22.528 21.504 50.688 48.128t57.856 53.248q68.608 63.488 153.6 142.336l0 194.56q0 22.528-16.896 39.936t-45.568 18.432l-193.536 0 0-158.72q0-33.792-31.744-33.792l-195.584 0q-17.408 0-24.064 10.24t-6.656 23.552q0 6.144-0.512 31.232t-0.512 53.76l0 73.728-187.392 0q-29.696 0-47.104-13.312t-17.408-37.888l0-203.776q83.968-76.8 152.576-139.264 28.672-26.624 57.344-52.736t52.224-47.616 39.424-36.352 19.968-18.944z"
                                p-id="1491"></path>
                        </svg>
                        <div class="iconTip">Home</div>
                    </a><a class="iconItem" href="<?php echo htmlspecialchars($githubUrl); ?>">
                        <svg t="1704870335945" class="icon" viewBox="0 0 1024 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="2487">
                            <path
                                d="M511.6 76.3C264.3 76.2 64 276.4 64 523.5 64 718.9 189.3 885 363.8 946c23.5 5.9 19.9-10.8 19.9-22.2v-77.5c-135.7 15.9-141.2-73.9-150.3-88.9C215 726 171.5 718 184.5 703c30.9-15.9 62.4 4 98.9 57.9 26.4 39.1 77.9 32.5 104 26 5.7-23.5 17.9-44.5 34.7-60.8-140.6-25.2-199.2-111-199.2-213 0-49.5 16.3-95 48.3-131.7-20.4-60.5 1.9-112.3 4.9-120 58.1-5.2 118.5 41.6 123.2 45.3 33-8.9 70.7-13.6 112.9-13.6 42.4 0 80.2 4.9 113.5 13.9 11.3-8.6 67.3-48.8 121.3-43.9 2.9 7.7 24.7 58.3 5.5 118 32.4 36.8 48.9 82.7 48.9 132.3 0 102.2-59 188.1-200 212.9 23.5 23.2 38.1 55.4 38.1 91v112.5c0.8 9 0 17.9 15 17.9 177.1-59.7 304.6-227 304.6-424.1 0-247.2-200.4-447.3-447.5-447.3z"
                                p-id="2488"></path>
                        </svg>
                        <div class="iconTip">Github</div>
                    </a><a class="iconItem" href="mailto:<?php echo htmlspecialchars($email); ?>">
                        <svg t="1704870588438" class="icon" viewBox="0 0 1024 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="3174">
                            <path
                                d="M926.47619 355.644952V780.190476a73.142857 73.142857 0 0 1-73.142857 73.142857H170.666667a73.142857 73.142857 0 0 1-73.142857-73.142857V355.644952l304.103619 257.828572a170.666667 170.666667 0 0 0 220.745142 0L926.47619 355.644952zM853.333333 170.666667a74.044952 74.044952 0 0 1 26.087619 4.778666 72.704 72.704 0 0 1 30.622477 22.186667 73.508571 73.508571 0 0 1 10.678857 17.67619c3.169524 7.509333 5.12 15.652571 5.607619 24.210286L926.47619 243.809524v24.380952L559.469714 581.241905a73.142857 73.142857 0 0 1-91.306666 2.901333l-3.632762-2.925714L97.52381 268.190476v-24.380952a72.899048 72.899048 0 0 1 40.155428-65.292191A72.97219 72.97219 0 0 1 170.666667 170.666667h682.666666z"
                                p-id="3175"></path>
                        </svg>
                        <div class="iconTip">Mail</div>
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
                <!-- 个人简介部分 -->
                <div class="about-section">
                    <div class="about-header">
                        <img src="<?php echo htmlspecialchars(getAboutInfo('avatar', './static/img/selfie.jpg')); ?>" alt="个人头像" class="about-avatar">
                        <div>
                            <div class="about-name"><?php echo htmlspecialchars(getAboutInfo('name', '您的姓名 (Your Name)')); ?></div>
                            <div class="about-title"><?php echo htmlspecialchars(getAboutInfo('title', '您的职位/身份描述')); ?></div>
                        </div>
                    </div>

                    <div class="about-bio">
                        <?php echo htmlspecialchars(getAboutInfo('bio', '嗨，我是[您的姓名]🤗，[您的学校/公司]的[您的专业/职位]。我的研究兴趣包括[研究领域1]、[研究领域2]和[研究领域3]。我喜欢业余时间在[平台1]、[平台2]和[平台3]等平台分享见解或捣鼓些奇奇怪怪的项目。')); ?>
                    </div>

                    <div class="section-title">技能</div>
                    <div class="skill-container">
                        <?php if (!empty($skills)): ?>
                            <?php foreach ($skills as $skill): ?>
                                <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="skill-tag">技能1</span>
                            <span class="skill-tag">技能2</span>
                            <span class="skill-tag">技能3</span>
                            <span class="skill-tag">技能4</span>
                            <span class="skill-tag">技能5</span>
                            <span class="skill-tag">技能6</span>
                            <span class="skill-tag">技能7</span>
                            <span class="skill-tag">技能8</span>
                            <span class="skill-tag">技能9</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 教育经历部分 -->
                <div class="about-section">
                    <div class="section-title">教育经历</div>
                    <div class="timeline">
                        <?php if (!empty($education)): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-date"><?php echo htmlspecialchars($education['period']); ?></div>
                            <div class="timeline-title"><?php echo htmlspecialchars($education['school']); ?></div>
                            <div class="timeline-content">
                                <?php echo htmlspecialchars($education['description']); ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-date">[开始年份] - [结束年份/至今]</div>
                            <div class="timeline-title">[学校名称]</div>
                            <div class="timeline-content">
                                [专业名称]专业。[研究方向描述]。
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Publications部分 -->
                <div class="about-section">
                    <div class="section-title">科研项目</div>
                    <div class="publications-wrapper">
                        <span class="publications-heading">😊 Publications</span>
                    </div>
                    <?php if (!empty($publications)): ?>
                        <?php foreach ($publications as $publication): ?>
                        <div class="publication-item">
                            <div class="publication-title"><a href="<?php echo htmlspecialchars($publication['url']); ?>" target="_blank"><?php echo htmlspecialchars($publication['title']); ?></a></div>
                            <div class="publication-authors"><?php echo htmlspecialchars($publication['authors']); ?></div>
                            <div class="publication-venue"><?php echo htmlspecialchars($publication['venue']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="publication-item">
                        <div class="publication-title"><a href="[论文链接]" target="_blank">[论文标题1]</a></div>
                        <div class="publication-authors">[作者列表1]</div>
                        <div class="publication-venue">[会议/期刊名称1]</div>
                    </div>
                    <div class="publication-item">
                        <div class="publication-title"><a href="[论文链接]" target="_blank">[论文标题2]</a></div>
                        <div class="publication-authors">[作者列表2]</div>
                        <div class="publication-venue">[会议/期刊名称2]</div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 荣誉奖项部分 -->
                <div class="about-section">
                    <div class="section-title">荣誉奖项</div>
                    <div class="timeline">
                        <?php if (!empty($awards)): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-date"><?php echo htmlspecialchars($awards['period']); ?></div>
                            <div class="timeline-title"><?php echo htmlspecialchars($awards['title']); ?></div>
                            <div class="timeline-content">
                                <?php echo htmlspecialchars($awards['description']); ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-date">[获奖年份]</div>
                            <div class="timeline-title">[奖项名称]</div>
                            <div class="timeline-content">
                                [奖项描述]。
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- 尾注 -->
    <footer>
        <?php echo htmlspecialchars(SiteConfigHelper::getFooterText()); ?>
    </footer>

    <div class="tc">
        <div class="tc-main">
            <img class="tc-img" src="" alt="" srcset="">
        </div>
    </div>

    <script src="./static/js/script.js"></script>
    <script src="./static/js/quotes.js"></script>
</body>

</html> 