@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="weui-desktop-page__title">远程附件配置</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                @else
                    <div class="padding" style="min-height: 460px">
                        @endif

                        <div class="fui-form">
                            <form action="{{ url('console/setting') }}" method="post" class="layui-form">
                                @csrf
                                <input type="hidden" name="op" value="remoteset">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">远程附件设置</label>
                                    <div class="layui-input-block">
                                        @foreach($attachs as $key=>$value)
                                            <input type="radio" lay-filter="ctrls" data-target=".remoteset" value="{{ $key }}" name="type" title="{{ $value }}"{{ $_W['setting']['remote']['type']==$key ? ' checked="checked"' : '' }}{{ in_array($key,array(1,3)) ? ' disabled' : '' }} />
                                        @endforeach
                                        <div class="layui-word-aux"><span class="text-red">设置后不建议频繁更改，如有变更请做好文件迁移工作</span></div>
                                    </div>
                                </div>
                                <div class="remoteset form-item2{{ $_W['setting']['remote']['type']==2 ? '' : ' layui-hide' }}">
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">Key ID</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="alioss[key]" value="{{ $_W['setting']['remote']['alioss']['key'] }}" placeholder="请输入阿里云Access Key ID" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">Key Secret</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="alioss[secret]" value="{{ $_W['setting']['remote']['alioss']['secret'] }}" placeholder="请输入阿里云Access Key Secret" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">内网传输</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="alioss[internal]" value="1" title="开启"{{ $_W['setting']['remote']['alioss']['internal']==1 ? ' checked' : '' }} />
                                            <input type="radio" name="alioss[internal]" value="0" title="关闭"{{ $_W['setting']['remote']['alioss']['internal']==1 ? '' : ' checked' }} />
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">Bucket名称</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="alioss[bucket]" value="{{ $_W['setting']['remote']['alioss']['bucket'] }}" placeholder="请输入阿里云存储桶，一般是 oss-cn-beijing/Bucket名称 这类的格式" autocomplete="off" class="layui-input">
                                            <div class="layui-word-aux">进入对象存储对应BUCKET的概览页面，复制页面网址的 bucket/ 到 /overview 中间的部分</div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">外网访问域名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="alioss[url]" value="{{ $_W['setting']['remote']['alioss']['url'] }}" placeholder="请输入默认外网访问域名（选填）" autocomplete="off" class="layui-input">
                                            <div class="layui-word-aux">http://或https://开头，结尾不要加/斜杠</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="remoteset form-item4{{ $_W['setting']['remote']['type']==4 ? '' : ' layui-hide' }}">
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">APPID</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="cos[appid]" value="{{ $_W['setting']['remote']['cos']['appid'] }}" placeholder="请输入腾讯云项目APPID" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">SecretID</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="cos[secretid]" value="{{ $_W['setting']['remote']['cos']['secretid'] }}" placeholder="请输入腾讯云项目SecretID" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">SecretKEY</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="cos[secretkey]" value="{{ $_W['setting']['remote']['cos']['secretkey'] }}" placeholder="请输入腾讯云项目SecretKEY" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">Bucket名称</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="cos[bucket]" value="{{ $_W['setting']['remote']['cos']['bucket'] }}" placeholder="请输入腾讯云存储的Bucket名称" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">所在地区</label>
                                        <div class="layui-input-block">
                                            <select name="cos[local]" class="layui-select" lay-search>
                                                <option value="ap-beijing-1"{{ $_W['setting']['remote']['cos']['local']=='ap-beijing-1'?' selected':'' }}>北京一区</option>
                                                <option value="ap-beijing"{{ $_W['setting']['remote']['cos']['local']=='ap-beijing'?' selected':'' }}>北京</option>
                                                <option value="ap-nanjing"{{ $_W['setting']['remote']['cos']['local']=='ap-nanjing'?' selected':'' }}>南京</option>
                                                <option value="ap-shanghai"{{ $_W['setting']['remote']['cos']['local']=='ap-shanghai'?' selected':'' }}>上海</option>
                                                <option value="ap-guangzhou"{{ $_W['setting']['remote']['cos']['local']=='ap-guangzhou'?' selected':'' }}>广州</option>
                                                <option value="ap-chengdu"{{ $_W['setting']['remote']['cos']['local']=='ap-chengdu'?' selected':'' }}>成都</option>
                                                <option value="ap-chongqing"{{ $_W['setting']['remote']['cos']['local']=='ap-chongqing'?' selected':'' }}>重庆</option>
                                                <option value="ap-shenzhen-fsi"{{ $_W['setting']['remote']['cos']['local']=='ap-shenzhen-fsi'?' selected':'' }}>深圳金融</option>
                                                <option value="ap-shanghai-fsi"{{ $_W['setting']['remote']['cos']['local']=='ap-beijing-1'?' selected':'' }}>上海金融</option>
                                                <option value="ap-beijing-fsi"{{ $_W['setting']['remote']['cos']['local']=='ap-shanghai-fsi'?' selected':'' }}>北京金融</option>
                                                <option value="ap-hongkong"{{ $_W['setting']['remote']['cos']['local']=='ap-hongkong'?' selected':'' }}>中国香港</option>
                                                <option value="ap-singapore"{{ $_W['setting']['remote']['cos']['local']=='ap-singapore'?' selected':'' }}>新加坡</option>
                                                <option value="ap-mumbai"{{ $_W['setting']['remote']['cos']['local']=='ap-mumbai'?' selected':'' }}>孟买</option>
                                                <option value="ap-seoul"{{ $_W['setting']['remote']['cos']['local']=='ap-seoul'?' selected':'' }}>首尔</option>
                                                <option value="ap-bangkok"{{ $_W['setting']['remote']['cos']['local']=='ap-bangkok'?' selected':'' }}>曼谷</option>
                                                <option value="ap-tokyo"{{ $_W['setting']['remote']['cos']['local']=='ap-tokyo'?' selected':'' }}>东京</option>
                                                <option value="na-siliconvalley"{{ $_W['setting']['remote']['cos']['local']=='ap-siliconvalley'?' selected':'' }}>硅谷</option>
                                                <option value="na-ashburn"{{ $_W['setting']['remote']['cos']['local']=='ap-ashburn'?' selected':'' }}>弗吉尼亚</option>
                                                <option value="na-toronto"{{ $_W['setting']['remote']['cos']['local']=='ap-toronto'?' selected':'' }}>多伦多</option>
                                                <option value="eu-frankfurt"{{ $_W['setting']['remote']['cos']['local']=='ap-frankfurt'?' selected':'' }}>法兰克福</option>
                                                <option value="eu-moscow"{{ $_W['setting']['remote']['cos']['local']=='ap-moscow'?' selected':'' }}>莫斯科</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">外网访问域名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="cos[url]" value="{{ $_W['setting']['remote']['cos']['url'] }}" placeholder="请输入默认外网访问域名" autocomplete="off" class="layui-input">
                                            <div class="layui-word-aux">http://或https://开头，结尾不要加/斜杠</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="remoteset form-item5{{ $_W['setting']['remote']['type']==5 ? '' : ' layui-hide' }}">
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <p class="layui-text">请编辑网站根目录下的 .env 文件配置亚马逊S3存储驱动</p>
                                            <p class="layui-text">需要配置 AWS_ACCESS_KEY_ID 、 AWS_SECRET_ACCESS_KEY 、 AWS_DEFAULT_REGION 、AWS_BUCKET 、AWS_URL</p>
                                            <p class="layui-text">如果您不了解该功能，请不要随意改动</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">保存</button>
                                        <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if(!$_W['isajax'])
                    </div>
            </div>
            @endif
        </div>

    @include('common.footer')
