# 数据库表结构文档

本文档记录了系统中所有数据表的结构信息，包括字段名、字段类型、默认值、额外属性和注释说明。

---

## 1. account 表

**表用途说明：** 账号关联表，用于管理不同平台账号（如微信公众号、小程序等）与统一账号的关联关系，记录账号的连接状态、过期时间等信息。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| acid | integer | - | primary, auto_increment | 账号关联ID |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| hash | string(8) | - | - | 账号哈希值 |
| type | boolean | - | - | 账号类型（公众号/小程序等） |
| isconnect | boolean | - | - | 是否已连接 |
| isdeleted | boolean | - | - | 是否已删除 |
| endtime | integer | - | unsigned | 账号到期时间戳 |
| send_account_expire_status | boolean | - | - | 账号过期通知发送状态 |
| send_api_expire_status | boolean | - | - | API过期通知发送状态 |

---

## 2. account_wechats 表

**表用途说明：** 微信公众号账号表，用于存储微信公众号的详细配置信息，包括公众号基本信息、认证信息、API密钥、授权令牌等，是微信公众号接入的核心配置表。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| acid | integer | - | primary, unsigned | 账号ID（关联account表） |
| uniacid | integer | - | unsigned | 统一账号ID（关联uni_account表） |
| token | string(32) | - | - | 微信Token |
| encodingaeskey | string | - | - | 消息加解密密钥 |
| level | boolean | - | - | 公众号类型（订阅号/服务号） |
| name | string(30) | - | - | 公众号名称 |
| account | string(30) | - | - | 公众号账号 |
| original | string(50) | - | - | 原始ID |
| signature | string(100) | - | - | 公众号签名 |
| country | string(10) | - | - | 国家 |
| province | string(3) | - | - | 省份 |
| city | string(15) | - | - | 城市 |
| username | string(30) | - | - | 微信用户名 |
| password | string(32) | - | - | 微信密码 |
| lastupdate | integer | - | unsigned | 最后更新时间戳 |
| key | string(50) | - | index | AppID |
| secret | string(50) | - | - | AppSecret |
| styleid | integer | - | unsigned | 样式ID |
| subscribeurl | string(120) | - | - | 关注链接 |
| auth_refresh_token | string | - | - | 授权刷新令牌 |

---

## 3. attachment_group 表

**表用途说明：** 附件分组表，用于对附件进行分类管理，支持树形结构的分组组织，便于用户对上传的文件进行分类和查找。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 分组ID |
| pid | integer | - | - | 父分组ID（支持树形结构） |
| name | string(25) | - | - | 分组名称 |
| uniacid | integer | - | - | 统一账号ID（关联uni_account表） |
| uid | integer | - | - | 用户ID（关联users表） |
| type | boolean | - | - | 分组类型 |

---

## 4. cache 表

**表用途说明：** 缓存表，Laravel框架的数据库缓存驱动表，用于存储应用程序的缓存数据，包括缓存键、值和过期时间，支持缓存过期管理。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| key | string | - | unique | 缓存键名 |
| value | mediumText | - | - | 缓存值（序列化数据） |
| expiration | integer | - | - | 过期时间戳 |

---

## 5. core_attachment 表

**表用途说明：** 核心附件表，用于存储系统中所有上传的附件文件信息，包括文件名、存储路径、文件类型、上传时间、所属分组等，是系统的文件管理中心。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 附件ID |
| uniacid | integer | - | unsigned | 统一账号ID（关联uni_account表） |
| uid | integer | - | unsigned | 上传用户ID（关联users表） |
| filename | string | - | - | 原始文件名 |
| attachment | string | - | - | 附件存储路径 |
| type | boolean | - | - | 附件类型（图片/文件等） |
| createtime | integer | - | unsigned | 上传时间戳 |
| module_upload_dir | string(100) | - | - | 模块上传目录 |
| group_id | integer | - | - | 所属分组ID（关联attachment_group表） |
| displayorder | integer | - | - | 显示排序顺序 |

---

## 6. core_paylog 表

**表用途说明：** 核心支付日志表，用于记录系统中所有的支付交易信息，包括支付类型、金额、状态、订单号、用户信息、优惠券使用情况等完整的支付流水记录。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| plid | bigInteger | - | primary, auto_increment, unsigned | 支付日志ID |
| type | string(20) | - | - | 支付类型 |
| uniacid | integer | - | index | 统一账号ID（关联uni_account表） |
| acid | integer | - | - | 账号ID（关联account表） |
| openid | string(40) | - | index | 用户OpenID |
| uniontid | string(64) | - | index | 联合交易号 |
| tid | string(128) | - | index | 交易订单号 |
| fee | decimal(10) | - | - | 支付金额 |
| status | boolean | - | - | 支付状态 |
| module | string(50) | - | - | 支付来源模块 |
| tag | string(2000) | - | - | 支付标签（扩展信息） |
| is_usecard | boolean | - | - | 是否使用会员卡 |
| card_type | boolean | - | - | 会员卡类型 |
| card_id | string(50) | - | - | 会员卡ID |
| card_fee | decimal(10) | - | unsigned | 会员卡抵扣金额 |
| encrypt_code | string(100) | - | - | 加密代码 |
| is_wish | boolean | - | - | 是否心愿单支付 |
| coupon | string(1000) | - | - | 优惠券信息（JSON格式） |

---

## 7. core_settings 表

**表用途说明：** 核心设置表，用于存储系统的核心配置参数，以键值对的形式保存全局系统设置，是系统配置管理的核心表。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| key | string(200) | - | primary | 配置键名 |
| value | text | - | - | 配置值（序列化数据） |

---

## 8. failed_jobs 表

**表用途说明：** 失败任务表，Laravel框架的失败任务记录表，用于存储执行失败的任务信息，包括连接信息、队列名称、任务载荷、异常信息和失败时间等，用于任务失败后的排查和重试。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | bigInteger | - | primary, auto_increment | 失败任务ID |
| connection | text | - | - | 连接名称 |
| queue | text | - | - | 队列名称 |
| payload | longText | - | - | 任务载荷（序列化数据） |
| exception | longText | - | - | 异常信息 |
| failed_at | timestamp | CURRENT_TIMESTAMP | - | 失败时间戳 |

---

## 9. gxswa_cloud 表

**表用途说明：** 云服务表，用于存储云服务相关的模块和应用信息，包括服务标识、名称、模块名、类型、Logo、网站、版本、发布日期等，用于云服务管理和应用商店功能。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释       |
|--------|---------|--------|---------|----------|
| id | integer | - | primary, auto_increment | 云服务ID    |
| identity | string(50) | '' | - | 服务标识     |
| name | string(50) | '' | - | 服务名称     |
| modulename | string(50) | '' | - | 模块名称     |
| type | boolean | 0 | - | 服务类型     |
| maintenance | boolean | - | - | 是否维护中    |
| logo | string | '' | - | Logo图片路径 |
| website | string | '' | - | 官方网站     |
| rootpath | string(50) | '' | - | 根路径      |
| version | string(20) | '' | - | 版本名称     |
| version_code | integer | 0 | - | 版本号      |
| online | text | NULL | nullable | 在线状态信息   |
| addtime | integer | 0 | - | 添加时间戳    |
| updatetime | string(10) | '0' | - | 更新时间     |
| dateline | integer | 0 | - | 数据时间线    |

---

## 10. jobs 表

**表用途说明：** 任务队列表，Laravel框架的队列任务表，用于存储待执行的异步任务，包括任务队列名称、任务载荷、尝试次数、保留时间和可用时间等，支持任务队列的延迟执行和重试机制。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | bigInteger | - | primary, auto_increment | 任务ID |
| queue | string | - | index | 队列名称 |
| payload | longText | - | - | 任务载荷（序列化数据） |
| attempts | unsignedTinyInteger | - | - | 尝试执行次数 |
| reserved_at | unsignedInteger | NULL | nullable | 保留时间戳（任务被处理时） |
| available_at | unsignedInteger | - | - | 可用时间戳（任务可执行时间） |
| created_at | unsignedInteger | - | - | 创建时间戳 |

---

## 11. mc_credits_record 表

**表用途说明：** 会员积分记录表，用于记录会员的所有积分变动记录，包括积分类型、变动数量、操作者、操作模块、操作时间、备注等完整的积分流水信息。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| uid | integer | - | unsigned, index | 会员ID（关联mc_members表） |
| uniacid | integer | - | index | 统一账号ID（关联uni_account表） |
| credittype | string(10) | - | - | 积分类型（credit1-credit6） |
| num | decimal(10) | - | - | 积分变动数量（正数为增加，负数为减少） |
| balance_after | decimal(10,2) | NULL | unsigned, nullable | 积分变动后的账户余额 |
| operator | integer | - | unsigned | 操作者ID |
| module | string(30) | - | - | 操作模块 |
| clerk_id | integer | - | unsigned | 店员ID |
| store_id | integer | - | unsigned | 门店ID |
| clerk_type | boolean | - | - | 店员类型 |
| createtime | integer | - | unsigned | 操作时间戳 |
| remark | string(200) | - | - | 备注说明 |
| real_uniacid | integer | - | - | 真实统一账号ID |

---

## 12. mc_groups 表

**表用途说明：** 会员组表，用于定义统一账号中的会员组配置，包括会员组名称、所需积分和是否为默认组等，用于会员等级管理。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| groupid | integer | - | primary, auto_increment | 会员组ID |
| uniacid | integer | - | index | 统一账号ID（关联uni_account表） |
| title | string(20) | - | - | 会员组名称 |
| credit | integer | - | unsigned | 所需积分 |
| isdefault | boolean | - | - | 是否为默认会员组 |

---

## 13. mc_mapping_fans 表

**表用途说明：** 会员粉丝映射表，用于建立微信公众号粉丝与系统会员之间的关联关系，存储粉丝的OpenID、昵称、关注状态、关注时间、标签等信息，是粉丝管理系统的核心表。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| fanid | integer | - | primary, auto_increment | 粉丝ID |
| acid | integer | - | unsigned, index | 账号ID（关联account表） |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| uid | integer | - | unsigned, index | 会员ID（关联mc_members表） |
| openid | string(50) | - | unique | 用户OpenID |
| nickname | string(50) | - | index | 粉丝昵称 |
| groupid | string(60) | - | - | 粉丝分组ID |
| salt | char(8) | - | - | 加密盐值 |
| follow | boolean | - | - | 是否关注 |
| followtime | integer | - | unsigned | 关注时间戳 |
| unfollowtime | integer | - | unsigned | 取消关注时间戳 |
| tag | string(1000) | - | - | 粉丝标签（JSON格式） |
| updatetime | integer | NULL | unsigned, nullable, index | 最后更新时间戳 |
| unionid | string(64) | - | - | 用户UnionID |
| user_from | boolean | - | - | 用户来源 |

---

## 14. mc_member_fields 表

**表用途说明：** 会员字段表，用于定义统一账号中会员资料的自定义字段配置，包括字段ID、字段标题、是否可用和显示顺序等，用于扩展会员信息字段。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| uniacid | integer | - | index | 统一账号ID（关联uni_account表） |
| fieldid | integer | - | index | 字段ID |
| title | string | - | - | 字段标题 |
| available | boolean | - | - | 是否可用 |
| displayorder | smallInteger | - | - | 显示排序顺序 |

---

## 15. mc_members 表

**表用途说明：** 会员中心会员表，用于存储统一账号中的会员信息，包括会员基本信息、联系方式、个人资料、积分余额等完整的会员数据，是会员管理系统的核心表。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| uid | integer | - | primary, auto_increment | 会员ID（关联用户ID） |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| mobile | string(18) | - | index | 手机号码 |
| email | string(50) | - | index | 电子邮箱 |
| password | string(32) | - | - | 密码（加密后） |
| salt | string(8) | - | - | 密码加密盐值 |
| groupid | integer | - | index | 会员组ID |
| credit1 | decimal(10) | - | unsigned | 积分1（如：积分） |
| credit2 | decimal(10) | - | unsigned | 积分2（如：余额） |
| credit3 | decimal(10) | - | unsigned | 积分3 |
| credit4 | decimal(10) | - | unsigned | 积分4 |
| credit5 | decimal(10) | - | unsigned | 积分5 |
| credit6 | decimal(10) | - | - | 积分6 |
| createtime | integer | - | unsigned | 注册时间戳 |
| realname | string(10) | - | - | 真实姓名 |
| nickname | string(20) | - | - | 昵称 |
| avatar | string | - | - | 头像图片路径 |
| qq | string(15) | - | - | QQ号码 |
| vip | boolean | - | - | 是否为VIP会员 |
| gender | boolean | - | - | 性别（0-女，1-男） |
| birthyear | smallInteger | - | unsigned | 出生年份 |
| birthmonth | boolean | - | - | 出生月份 |
| birthday | boolean | - | - | 出生日期 |
| constellation | string(10) | - | - | 星座 |
| zodiac | string(5) | - | - | 生肖 |
| telephone | string(15) | - | - | 固定电话 |
| idcard | string(30) | - | - | 身份证号码 |
| studentid | string(50) | - | - | 学号 |
| grade | string(10) | - | - | 年级 |
| address | string | - | - | 详细地址 |
| zipcode | string(10) | - | - | 邮政编码 |
| nationality | string(30) | - | - | 国籍 |
| resideprovince | string(30) | - | - | 居住省份 |
| residecity | string(30) | - | - | 居住城市 |
| residedist | string(30) | - | - | 居住区县 |
| graduateschool | string(50) | - | - | 毕业学校 |
| company | string(50) | - | - | 公司名称 |
| education | string(10) | - | - | 学历 |
| occupation | string(30) | - | - | 职业 |
| position | string(30) | - | - | 职位 |
| revenue | string(10) | - | - | 收入水平 |
| affectivestatus | string(30) | - | - | 情感状态 |
| lookingfor | string | - | - | 寻找 |
| bloodtype | string(5) | - | - | 血型 |
| height | string(5) | - | - | 身高（cm） |
| weight | string(5) | - | - | 体重（kg） |
| alipay | string(30) | - | - | 支付宝账号 |
| msn | string(30) | - | - | MSN账号 |
| taobao | string(30) | - | - | 淘宝账号 |
| site | string(30) | - | - | 个人网站 |
| bio | text | - | - | 个人简介 |
| interest | text | - | - | 兴趣爱好 |
| pay_password | string(30) | - | - | 支付密码 |
| user_from | boolean | - | - | 用户来源 |

---

## 16. microserver 表

**表用途说明：** 微服务表，用于存储系统中安装的微服务信息，包括服务标识、名称、封面、摘要、版本、发布版本、驱动类型、入口地址、数据和配置等，是微服务管理系统的核心表。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 微服务ID |
| identity | string(20) | - | - | 服务标识（唯一标识） |
| name | string(20) | - | - | 服务名称 |
| cover | string(255) | '' | - | 封面图片路径 |
| summary | text | NULL | nullable | 服务摘要描述 |
| version | string(10) | '' | - | 版本号 |
| releases | string(20) | '' | - | 发布版本 |
| drive | string(10) | 'php' | - | 驱动类型 |
| entrance | string(255) | '' | - | 入口地址 |
| datas | mediumText | NULL | nullable | 数据配置（序列化数据） |
| configs | mediumText | NULL | nullable | 配置信息（序列化数据） |
| status | boolean | 1 | - | 状态（1=启用，0=禁用） |
| addtime | integer | 0 | unsigned | 添加时间戳 |
| dateline | integer | 0 | unsigned | 数据时间戳 |

---

## 17. microserver_data 表

**表用途说明：** 微服务数据表，用于存储统一账号中微服务的配置数据，以键值对的形式保存每个统一账号的微服务设置信息，支持微服务的个性化配置管理。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | bigInteger | - | primary, auto_increment | 数据记录ID |
| uniacid | integer | 0 | unsigned, index(uniacid) | 统一账号ID（关联uni_account表） |
| name | string(20) | - | index(uniacid) | 配置名称（键名） |
| data | mediumText | - | - | 配置数据（序列化数据） |
| addtime | integer | 0 | unsigned | 添加时间戳 |
| dateline | integer | 0 | unsigned | 数据时间戳 |

---

## 18. microserver_unilink 表

**表用途说明：** 微服务统一链接表，用于建立微服务与统一账号系统的关联关系，存储微服务的名称、标题、封面、摘要、入口地址、权限配置等信息，实现微服务在统一账号中的集成和管理。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 关联记录ID |
| name | string(20) | - | - | 微服务名称（关联microserver表） |
| title | string(20) | - | - | 微服务标题 |
| cover | string(255) | '' | - | 封面图片路径 |
| summary | string(255) | '' | - | 服务摘要 |
| entry | string(255) | '' | - | 入口地址 |
| perms | mediumText | NULL | nullable | 权限配置（序列化数据） |
| status | boolean | 1 | - | 状态（1=启用，0=禁用） |
| addtime | integer | 0 | unsigned | 添加时间戳 |
| dateline | integer | 0 | unsigned | 数据时间戳 |

---

## 19. modules 表

**表用途说明：** 模块管理表，存储系统中所有功能模块的详细信息，包括模块名称、版本、作者、描述、权限、支持的平台类型等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| mid | integer | - | primary, auto_increment | 模块ID |
| name | string(100) | - | index | 模块名称（唯一标识） |
| application_type | boolean | - | - | 应用类型 |
| type | string(20) | - | - | 模块类型 |
| title | string(100) | - | - | 模块标题 |
| version | string(15) | - | - | 模块版本号 |
| ability | string(500) | - | - | 模块功能描述 |
| description | string(1000) | - | - | 模块详细描述 |
| author | string(50) | - | - | 模块作者 |
| url | string | - | - | 模块官网地址 |
| settings | boolean | - | - | 是否有设置项 |
| subscribes | string(500) | - | - | 订阅的事件列表 |
| handles | string(500) | - | - | 处理的事件列表 |
| isrulefields | boolean | - | - | 是否有规则字段 |
| issystem | boolean | - | - | 是否为系统模块 |
| target | integer | - | unsigned | 目标类型 |
| iscard | boolean | - | - | 是否支持会员卡 |
| permissions | string(5000) | - | - | 模块权限配置（JSON格式） |
| title_initial | string(1) | - | - | 标题首字母（用于排序） |
| wxapp_support | boolean | - | - | 是否支持微信小程序 |
| welcome_support | integer | - | - | 欢迎页支持类型 |
| oauth_type | boolean | - | - | OAuth授权类型 |
| webapp_support | boolean | - | - | 是否支持Web应用 |
| phoneapp_support | boolean | - | - | 是否支持手机APP |
| account_support | boolean | - | - | 是否支持账号类型 |
| xzapp_support | boolean | - | - | 是否支持XZ应用 |
| aliapp_support | boolean | - | - | 是否支持支付宝小程序 |
| logo | string(250) | - | - | 模块Logo路径 |
| baiduapp_support | boolean | - | - | 是否支持百度小程序 |
| toutiaoapp_support | boolean | - | - | 是否支持头条小程序 |
| from | string(10) | - | - | 模块来源 |
| cloud_record | boolean | - | - | 是否支持云记录 |
| sections | integer | - | unsigned | 模块分类 |
| label | string(500) | - | - | 模块标签 |
| status | boolean | - | - | 模块状态（启用/禁用） |

---

## 20. modules_recycle 表

**表用途说明：** 模块回收站表，用于存储已删除的模块信息，包括模块名称、类型和支持的应用平台等，用于模块的恢复和审计功能。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| name | string | - | - | 模块名称 |
| type | boolean | - | - | 模块类型 |
| account_support | boolean | - | - | 是否支持账号类型 |
| wxapp_support | boolean | - | - | 是否支持微信小程序 |
| welcome_support | boolean | - | - | 是否支持欢迎页 |
| webapp_support | boolean | - | - | 是否支持Web应用 |
| phoneapp_support | boolean | - | - | 是否支持手机APP |
| xzapp_support | boolean | - | - | 是否支持XZ应用 |
| aliapp_support | boolean | - | - | 是否支持支付宝小程序 |
| baiduapp_support | boolean | - | - | 是否支持百度小程序 |
| toutiaoapp_support | boolean | - | - | 是否支持头条小程序 |

---

## 21. sessions 表

**表用途说明：** 用户会话表，用于存储用户的会话信息，包括会话ID、用户ID、IP地址、用户代理和会话数据等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | string | - | unique | 会话唯一标识符 |
| user_id | unsignedBigInteger | NULL | nullable | 关联的用户ID |
| ip_address | string(45) | NULL | nullable | 用户IP地址 |
| user_agent | text | NULL | nullable | 用户代理信息（浏览器信息） |
| payload | text | - | - | 会话数据载荷 |
| last_activity | integer | - | - | 最后活动时间戳 |

---

## 22. site_multi 表

**表用途说明：** 多站点表，用于管理统一账号下的多个站点配置，包括站点标题、样式ID、站点信息和绑定域名等，支持一个统一账号管理多个独立站点。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 站点ID |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| title | string(30) | - | - | 站点标题 |
| styleid | integer | - | unsigned | 样式ID |
| site_info | text | - | - | 站点信息（JSON格式） |
| status | boolean | - | - | 站点状态（启用/禁用） |
| bindhost | string | - | index | 绑定域名 |

---

## 23. site_store_create_account 表

**表用途说明：** 站点商店创建账号表，用于记录用户在站点商店中创建的账号信息，包括用户ID、统一账号ID、账号类型和到期时间等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| uid | integer | - | - | 用户ID（关联users表） |
| uniacid | integer | - | - | 统一账号ID（关联uni_account表） |
| type | boolean | - | - | 账号类型 |
| endtime | integer | - | - | 到期时间戳 |

---

## 24. system_logs 表

**表用途说明：** 系统日志表，用于记录系统的操作日志，包括日志类型、操作模块、操作标题、操作详情、操作人信息、IP地址、请求信息、执行状态、错误码、耗时等完整的系统操作审计信息。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | bigInteger | - | primary, auto_increment | 日志ID |
| type | string(20) | - | index | 日志类型 |
| module | string(50) | - | - | 操作模块 |
| title | string | - | - | 日志标题 |
| content | text | NULL | nullable | 日志详情 |
| user_id | unsignedInteger | NULL | nullable, index | 操作人ID |
| username | string(50) | NULL | nullable | 操作人用户名 |
| ip | string(45) | NULL | nullable | 操作IP地址 |
| url | text | NULL | nullable | 请求URL |
| method | string(10) | NULL | nullable | 请求方法 |
| status | boolean | 1 | - | 状态（1=成功，0=失败），index |
| error_code | string(50) | NULL | nullable | 错误码 |
| cost_ms | unsignedInteger | NULL | nullable | 耗时（毫秒） |
| extra | text | NULL | nullable | 额外信息（JSON格式） |
| created_at | timestamp | NULL | nullable, index | 创建时间 |
| updated_at | timestamp | NULL | nullable | 更新时间 |

---

## 25. uni_account 表

**表用途说明：** 统一账号表，用于管理平台上的统一账号信息，包括账号名称、描述、默认账号、创建信息、Logo和二维码等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| uniacid | integer | - | primary, auto_increment | 统一账号ID |
| groupid | integer | - | - | 账号组ID |
| name | string(100) | - | - | 账号名称 |
| description | string | - | - | 账号描述 |
| default_acid | integer | - | unsigned | 默认关联账号ID（关联account表） |
| rank | integer | NULL | nullable | 排序权重 |
| title_initial | string(1) | - | - | 标题首字母（用于排序） |
| createtime | integer | - | - | 创建时间戳 |
| logo | string | - | - | Logo图片路径 |
| qrcode | string | - | - | 二维码图片路径 |
| create_uid | integer | - | - | 创建者用户ID |

---

## 26. uni_account_extra_modules 表

**表用途说明：** 统一账号额外模块表，用于存储统一账号的额外扩展模块列表，通常用于存储临时或特殊授权的模块信息。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| modules | text | - | - | 额外模块列表（JSON格式或序列化数据） |

---

## 27. uni_account_group 表

**表用途说明：** 统一账号组关联表，用于建立统一账号与账号组之间的多对多关联关系，实现账号的分组管理。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 关联记录ID |
| uniacid | integer | - | unsigned | 统一账号ID（关联uni_account表） |
| groupid | integer | - | - | 账号组ID（关联uni_group表） |

---

## 28. uni_account_modules 表

**表用途说明：** 统一账号模块关联表，用于管理每个统一账号已安装和启用的功能模块，包括模块启用状态、配置信息、快捷方式和显示顺序等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 关联记录ID |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| module | string(50) | - | index | 模块名称（关联modules表） |
| enabled | boolean | - | - | 是否启用该模块 |
| settings | text | - | - | 模块配置信息（JSON格式） |
| shortcut | boolean | - | - | 是否显示在快捷方式 |
| displayorder | integer | - | unsigned | 显示排序顺序 |

---

## 29. uni_account_modules_shortcut 表

**表用途说明：** 统一账号模块快捷方式表，用于存储统一账号中模块的快捷方式配置，包括标题、URL、图标等信息，方便用户快速访问常用模块功能。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 快捷方式ID |
| title | string(200) | - | - | 快捷方式标题 |
| url | string(250) | - | - | 快捷方式链接地址 |
| icon | string(200) | - | - | 快捷方式图标路径 |
| uniacid | integer | - | - | 统一账号ID（关联uni_account表） |
| version_id | integer | - | - | 版本ID |
| module_name | string(200) | - | - | 模块名称（关联modules表） |

---

## 30. uni_account_users 表

**表用途说明：** 统一账号用户关联表，用于管理哪些用户拥有哪些统一账号的访问权限，包括用户角色、权限等级和入口权限等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 关联记录ID |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| uid | integer | - | unsigned, index | 用户ID（关联users表） |
| role | string | '' | - | 用户角色（如管理员、编辑等） |
| rank | boolean | - | - | 权限等级 |
| entrance | string(50) | '' | - | 入口权限标识 |

---

## 31. uni_group 表

**表用途说明：** 统一账号组表，用于管理统一账号的分组，每个组可以包含多个统一账号，并配置该组的模块权限和模板权限，实现账号的批量管理和权限分配。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 账号组ID |
| owner_uid | integer | - | - | 所属用户ID（创建者） |
| name | string(50) | - | - | 账号组名称 |
| modules | text | - | - | 模块权限列表（JSON格式） |
| templates | string(5000) | - | - | 模板权限列表（JSON格式） |
| uniacid | integer | - | unsigned, index | 统一账号ID（关联uni_account表） |
| uid | integer | - | - | 用户ID（关联users表） |

---

## 32. uni_modules 表

**表用途说明：** 统一账号模块表，用于记录统一账号与模块的关联关系，存储每个统一账号已安装的模块列表。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 关联记录ID |
| uniacid | integer | - | index | 统一账号ID（关联uni_account表） |
| module_name | string(50) | - | - | 模块名称（关联modules表） |

---

## 33. uni_settings 表

**表用途说明：** 统一账号设置表，存储每个统一账号的系统配置信息，包括OAuth认证、支付配置、通知设置、积分体系、默认消息、统计设置、附件限制等全面的账号配置参数。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| uniacid | integer | - | primary, unsigned | 统一账号ID（关联uni_account表） |
| passport | string(200) | - | - | 通行证配置 |
| oauth | string(100) | - | - | OAuth认证配置 |
| jsauth_acid | integer | - | unsigned | JS授权账号ID |
| notify | string(2000) | - | - | 通知配置（JSON格式） |
| creditnames | string(500) | - | - | 积分名称配置 |
| creditbehaviors | string(500) | - | - | 积分行为配置 |
| welcome | string(60) | - | - | 欢迎消息配置 |
| default | string(60) | - | - | 默认配置标识 |
| default_message | string(2000) | - | - | 默认消息内容 |
| payment | text | - | - | 支付配置（JSON格式） |
| notice | text | - | - | 通知模板配置 |
| stat | string(300) | - | - | 统计配置 |
| default_site | integer | NULL | unsigned, nullable | 默认站点ID |
| sync | boolean | - | - | 是否同步配置 |
| recharge | string(500) | - | - | 充值配置 |
| tplnotice | string(2000) | - | - | 模板通知配置 |
| grouplevel | boolean | - | - | 是否启用组级别配置 |
| mcplugin | string(500) | - | - | 会员中心插件配置 |
| exchange_enable | boolean | - | - | 是否启用兑换功能 |
| coupon_type | boolean | - | - | 优惠券类型配置 |
| menuset | text | - | - | 菜单设置（JSON格式） |
| statistics | string(100) | - | - | 统计功能配置 |
| bind_domain | string(200) | - | - | 绑定域名 |
| comment_status | boolean | - | - | 评论功能状态 |
| reply_setting | boolean | - | - | 回复设置 |
| default_module | string(100) | - | - | 默认模块名称 |
| attachment_limit | integer | - | - | 附件数量限制 |
| attachment_size | string(20) | - | - | 附件大小限制 |
| sync_member | boolean | - | - | 是否同步会员信息 |
| remote | string(2000) | - | - | 远程服务配置 |

---

## 34. users 表

**表用途说明：** 系统用户表，存储平台所有用户的基本信息，包括登录凭证、用户状态、权限组、注册信息、访问记录等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| uid | integer | - | primary, auto_increment | 用户唯一标识ID |
| owner_uid | integer | - | - | 所属用户ID（用于子账户管理） |
| groupid | integer | - | unsigned | 用户组ID |
| founder_groupid | boolean | - | - | 是否为创始人组 |
| username | string(30) | - | unique | 用户名（登录账号） |
| password | string(200) | - | - | 密码（加密后） |
| salt | string(10) | - | - | 密码加密盐值 |
| type | boolean | - | - | 用户类型 |
| status | boolean | - | - | 用户状态（启用/禁用） |
| joindate | integer | - | unsigned | 注册时间戳 |
| joinip | string(15) | - | - | 注册IP地址 |
| lastvisit | integer | - | unsigned | 最后访问时间戳 |
| lastip | string(15) | - | - | 最后访问IP地址 |
| remark | string(500) | - | - | 备注信息 |
| starttime | integer | - | unsigned | 账户生效开始时间戳 |
| endtime | integer | - | unsigned | 账户到期时间戳 |
| register_type | boolean | - | - | 注册类型 |
| openid | string(50) | - | - | 第三方登录OpenID |
| welcome_link | boolean | - | - | 是否显示欢迎链接 |
| notice_setting | string(5000) | - | - | 通知设置（JSON格式） |
| is_bind | boolean | - | - | 是否已绑定 |
| remember_token | string(100) | NULL | nullable | 记住我功能的令牌 |

---

## 35. users_bind 表

**表用途说明：** 用户绑定表，用于管理用户与第三方平台账号的绑定关系，支持多种第三方登录和账号关联功能。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 绑定记录ID |
| uid | integer | - | index | 用户ID（关联users表） |
| bind_sign | string(50) | - | index | 绑定标识（第三方平台唯一标识） |
| third_type | boolean | - | - | 第三方类型（如微信、QQ、支付宝等） |
| third_nickname | string | - | - | 第三方平台昵称 |

---

## 36. users_create_group 表

**表用途说明：** 用户创建组表，用于存储用户自定义创建的账号组配置，定义该组可创建的各类应用数量限制，实现灵活的账号组管理。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 创建组ID |
| group_name | string(50) | - | - | 组名称 |
| maxaccount | integer | - | - | 最大账号数量限制 |
| maxwxapp | integer | - | - | 最大微信小程序数量 |
| maxwebapp | integer | - | - | 最大Web应用数量 |
| maxphoneapp | integer | - | - | 最大手机APP数量 |
| maxxzapp | integer | - | - | 最大XZ应用数量 |
| maxaliapp | integer | - | - | 最大支付宝小程序数量 |
| createtime | integer | - | - | 创建时间戳 |
| maxbaiduapp | integer | - | - | 最大百度小程序数量 |
| maxtoutiaoapp | integer | - | - | 最大头条小程序数量 |

---

## 37. users_extra_group 表

**表用途说明：** 用户额外组表，用于管理用户额外分配的账号组权限，实现用户跨组访问和管理多个账号组的功能。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| uid | integer | - | index | 用户ID（关联users表） |
| uni_group_id | integer | - | index | 统一账号组ID（关联uni_group表） |
| create_group_id | integer | - | index | 创建组ID（关联users_create_group表） |

---

## 38. users_extra_limit 表

**表用途说明：** 用户额外限制表，用于存储用户额外的资源配额限制，可以覆盖用户组的基础限制，实现个性化的资源分配。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| uid | integer | - | index | 用户ID（关联users表） |
| maxaccount | integer | - | - | 额外最大账号数量 |
| maxwxapp | integer | - | - | 额外最大微信小程序数量 |
| maxwebapp | integer | - | - | 额外最大Web应用数量 |
| maxphoneapp | integer | - | - | 额外最大手机APP数量 |
| maxxzapp | integer | - | - | 额外最大XZ应用数量 |
| maxaliapp | integer | - | - | 额外最大支付宝小程序数量 |
| timelimit | integer | - | - | 额外时间限制（天数） |
| maxbaiduapp | integer | - | - | 额外最大百度小程序数量 |
| maxtoutiaoapp | integer | - | - | 额外最大头条小程序数量 |

---

## 39. users_extra_modules 表

**表用途说明：** 用户额外模块表，用于存储用户额外授权或购买的功能模块信息，记录用户可使用的扩展模块及其支持的应用类型。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| uid | integer | - | index | 用户ID（关联users表） |
| module_name | string(100) | - | index | 模块名称（关联modules表） |
| support | string(50) | - | - | 支持的应用类型（如wxapp、webapp等） |

---

## 40. users_failed_login 表

**表用途说明：** 用户登录失败表，用于记录登录失败的尝试记录，包括IP地址、用户名和失败次数，用于防止暴力破解和账户安全保护。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 记录ID |
| ip | string(15) | - | index(ip_username) | 登录IP地址 |
| username | string(32) | - | index(ip_username) | 登录用户名 |
| count | boolean | - | - | 失败次数 |
| lastupdate | integer | - | unsigned | 最后更新时间戳 |

---

## 41. users_founder_group 表

**表用途说明：** 创始人组表，用于定义创始人用户组的权限套餐和资源限制配置，包括可创建的账号数量、各类应用数量限制和时间限制等。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 创始人组ID |
| name | string(50) | - | - | 创始人组名称 |
| package | string(5000) | - | - | 套餐配置（JSON格式） |
| maxaccount | integer | - | unsigned | 最大账号数量限制 |
| timelimit | integer | - | unsigned | 时间限制（天数） |
| maxwxapp | integer | - | unsigned | 最大微信小程序数量 |
| maxwebapp | integer | - | - | 最大Web应用数量 |
| maxphoneapp | integer | - | - | 最大手机APP数量 |
| maxxzapp | integer | - | - | 最大XZ应用数量 |
| maxaliapp | integer | - | - | 最大支付宝小程序数量 |
| maxbaiduapp | integer | - | - | 最大百度小程序数量 |
| maxtoutiaoapp | integer | - | - | 最大头条小程序数量 |

---

## 42. users_founder_own_users 表

**表用途说明：** 创始人拥有用户表，用于建立创始人与普通用户之间的拥有关系，记录哪些用户属于哪个创始人管理，实现多级用户管理体系。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 关联记录ID |
| uid | integer | - | index | 用户ID（关联users表） |
| founder_uid | integer | - | index | 创始人用户ID（关联users表） |

---

## 43. users_group 表

**表用途说明：** 用户组表，用于定义不同用户组的权限套餐和资源限制，包括可创建的账号数量、各类应用数量限制、时间限制等配置信息。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 用户组ID |
| owner_uid | integer | - | - | 所属用户ID（创建者） |
| name | string(50) | - | - | 用户组名称 |
| package | string(5000) | - | - | 套餐配置（JSON格式） |
| maxaccount | integer | - | unsigned | 最大账号数量限制 |
| timelimit | integer | - | unsigned | 时间限制（天数） |
| maxwxapp | integer | - | unsigned | 最大微信小程序数量 |
| maxwebapp | integer | - | - | 最大Web应用数量 |
| maxphoneapp | integer | - | - | 最大手机APP数量 |
| maxxzapp | integer | - | - | 最大XZ应用数量 |
| maxaliapp | integer | - | - | 最大支付宝小程序数量 |
| maxbaiduapp | integer | - | - | 最大百度小程序数量 |
| maxtoutiaoapp | integer | - | - | 最大头条小程序数量 |

---

## 44. users_login_logs 表

**表用途说明：** 用户登录日志表，用于记录用户成功登录的详细信息，包括登录IP、登录城市和登录时间，用于安全审计和登录行为分析。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 日志记录ID |
| uid | integer | - | unsigned | 用户ID（关联users表） |
| ip | string(15) | - | - | 登录IP地址 |
| city | string(256) | - | - | 登录城市 |
| createtime | integer | - | unsigned | 登录时间戳 |

---

## 45. users_operate_history 表

**表用途说明：** 用户操作历史表，用于记录用户在统一账号中对各个模块的操作历史，便于追踪用户的使用行为和操作记录。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 操作记录ID |
| type | boolean | - | index | 操作类型 |
| uid | integer | - | unsigned, index | 用户ID（关联users表） |
| uniacid | integer | - | unsigned | 统一账号ID（关联uni_account表） |
| module_name | string(100) | - | - | 模块名称（关联modules表） |
| createtime | integer | - | - | 操作时间戳 |

---

## 46. users_operate_star 表

**表用途说明：** 用户操作收藏表，用于记录用户收藏或标记的模块，支持用户对常用模块进行收藏和排序管理。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 收藏记录ID |
| type | boolean | - | index | 收藏类型 |
| uid | integer | - | unsigned, index | 用户ID（关联users表） |
| uniacid | integer | - | unsigned | 统一账号ID（关联uni_account表） |
| module_name | string(100) | - | - | 模块名称（关联modules表） |
| rank | integer | - | - | 排序权重 |
| createtime | integer | - | - | 收藏时间戳 |

---

## 47. users_permission 表

**表用途说明：** 用户权限表，用于存储用户在统一账号中的详细权限配置，包括操作权限、URL访问权限、模块权限和模板权限等细粒度的权限控制。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 权限记录ID |
| uniacid | integer | - | unsigned | 统一账号ID（关联uni_account表） |
| uid | integer | - | unsigned | 用户ID（关联users表） |
| type | string(100) | - | - | 权限类型 |
| permission | string(10000) | - | - | 权限配置（JSON格式） |
| url | string | - | - | URL访问权限 |
| modules | text | - | - | 模块权限列表（JSON格式） |
| templates | text | - | - | 模板权限列表（JSON格式） |

---

## 48. users_profile 表

**表用途说明：** 用户资料表，存储用户的详细个人信息，包括基本信息、联系方式、教育背景、工作信息、个人兴趣等完整的用户档案数据。

| 字段名 | 字段类型 | 默认值 | 额外属性 | 注释 |
|--------|---------|--------|---------|------|
| id | integer | - | primary, auto_increment | 资料记录ID |
| uid | integer | - | unsigned | 用户ID（关联users表） |
| createtime | integer | - | unsigned | 创建时间戳 |
| edittime | integer | - | - | 最后编辑时间戳 |
| realname | string(10) | - | - | 真实姓名 |
| nickname | string(20) | - | - | 昵称 |
| avatar | string | - | - | 头像图片路径 |
| qq | string(15) | - | - | QQ号码 |
| mobile | string(11) | - | - | 手机号码 |
| fakeid | string(30) | - | - | 虚拟ID |
| vip | boolean | - | - | 是否为VIP用户 |
| gender | boolean | - | - | 性别（0-女，1-男） |
| birthyear | smallInteger | - | unsigned | 出生年份 |
| birthmonth | boolean | - | - | 出生月份 |
| birthday | boolean | - | - | 出生日期 |
| constellation | string(10) | - | - | 星座 |
| zodiac | string(5) | - | - | 生肖 |
| telephone | string(15) | - | - | 固定电话 |
| idcard | string(30) | - | - | 身份证号码 |
| studentid | string(50) | - | - | 学号 |
| grade | string(10) | - | - | 年级 |
| address | string | - | - | 详细地址 |
| zipcode | string(10) | - | - | 邮政编码 |
| nationality | string(30) | - | - | 国籍 |
| resideprovince | string(30) | - | - | 居住省份 |
| residecity | string(30) | - | - | 居住城市 |
| residedist | string(30) | - | - | 居住区县 |
| graduateschool | string(50) | - | - | 毕业学校 |
| company | string(50) | - | - | 公司名称 |
| education | string(10) | - | - | 学历 |
| occupation | string(30) | - | - | 职业 |
| position | string(30) | - | - | 职位 |
| revenue | string(10) | - | - | 收入水平 |
| affectivestatus | string(30) | - | - | 情感状态 |
| lookingfor | string | - | - | 寻找 |
| bloodtype | string(5) | - | - | 血型 |
| height | string(5) | - | - | 身高（cm） |
| weight | string(5) | - | - | 体重（kg） |
| alipay | string(30) | - | - | 支付宝账号 |
| msn | string(30) | - | - | MSN账号 |
| email | string(50) | - | - | 电子邮箱 |
| taobao | string(30) | - | - | 淘宝账号 |
| site | string(30) | - | - | 个人网站 |
| bio | text | - | - | 个人简介 |
| interest | text | - | - | 兴趣爱好 |
| workerid | string(64) | - | - | 工号 |
| is_send_mobile_status | boolean | - | - | 是否已发送手机验证状态 |
| send_expire_status | boolean | - | - | 过期通知发送状态 |

---
