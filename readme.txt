=== NanoCat ===
Contributors: nanocat
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 3.5.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: blog, one-column, two-columns, right-sidebar, custom-colors, custom-logo, custom-menu, editor-style, featured-images, footer-widgets, threaded-comments, translation-ready, block-styles, wide-blocks

玻璃拟态、二次元风的 WordPress 博客主题：1200px 栅格、280px 侧栏、随机壁纸与磨砂玻璃、Sakana 角色、Swup 无刷新导航，内置 AI 工作台、状态页、友链 RSS、专栏与 Wiki。

== Description ==

NanoCat 把博客做成一个玻璃阅读室：固定随机壁纸、半透明磨砂面板、文章列表、状态监控和 AI 对话都在同一套视觉里。

主要特性：

* Vivia 玻璃拟态：半透明面板、30px 背景模糊、颗粒与高光。
* 1200px 栅格与 280px 侧栏，桌面 / 平板 / 手机逐档适配。
* 7 张本地随机壁纸，加载屏预载，不请求第三方 API。
* Swup 无刷新导航：页头、弹窗、壁纸和 Sakana 在站内跳转时保持常驻。
* 首页文章列表：日期、分类、字数、摘要与阅读箭头。
* 友链 RSS：宽卡片显示好友签名与最近 3 篇文章，按更新时间排序。
* 状态页：探测站点、RSS、REST、AI、静态资源与数据库，保留 30 天历史。
* 专栏与 Wiki：行列表 + 最近文章 / 子页面，详情页有目录树和上下篇。
* AI 原生体验：全局对话、SSE 流式回答、文章伴读、选中追问。
* 社交链接：GitHub、Bilibili、邮箱与 RSS。
* 页头玻璃品牌标、问 AI 胶囊与同尺寸搜索圆钮。
* 左下角网易云歌单：67 首免费曲直接站内播放，切页不断。
* 页脚可配置 ICP 备案号、「安全运行 N 天」徽章与主题署名。
* 无外部字体、无 CDN、无构建步骤。

== Installation ==

1. 上传主题并启用。
2. 在「外观 → 自定义 → NanoCat 主题选项」设置资料、社交链接和页脚。
3. 分配主导航与页脚导航。
4. 首次启用会自动创建归档、专栏、友链和 AI 工作台页面。
5. 新建 slug 为 `status` 的页面并选择 Status 模板。
6. 在链接管理中填写好友 RSS，刷新缓存。

== Frequently Asked Questions ==

= 为什么修改 PHP 后页面没变？ =

服务器开启 OPcache 且 `validate_timestamps=0` 时需要重启 PHP-FPM。

= 壁纸可以固定吗？ =

把 `assets/images/wallpapers/` 里的图换成自己的图片即可；加载屏会随机预载其中一张。

= 状态页数据从哪来？ =

主题每小时探测一次站内服务，历史保存在 option 中；打开页面或点刷新也会触发检测。

== Changelog ==

= 3.5.1 =
* 移动端文章页先显示正文，侧栏的资料卡、分类和社交链接不再占据首屏。
* 首页与内页在 ≤900px 隐藏右侧栏，阅读顺序更直接。

= 3.5.0 =
* 全站 Vivia 玻璃拟态与磨砂质感。
* Swup 无刷新导航与页面级重初始化。
* 新增状态页 /status/。
* 壁纸本地化，Sakana 自托管并提前挂载。
* 友链宽卡片 + 最近 3 篇 RSS。
* Wiki / 专栏文章式行列表与玻璃详情页。
* 随机壁纸、Sakana、加载屏、页脚胶囊和四社交图标。
