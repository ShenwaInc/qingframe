/**
 * 地区选择器功能
 * 支持省份、城市、区县三级联动选择
 */

// 全局变量
var addressData = null;
var cityData = {};
var distData = {};

/**
 * 初始化地区选择器
 * @param {string} name 字段名前缀
 * @param {object} selected 已选择的地区
 * @param {object} options 配置选项
 */
function initRegionSelector(name, selected, options) {
    console.log('初始化地区选择器:', name, selected, options);
    
    // 加载地址数据
    $.getJSON(options.json, function(data) {
        console.log('地址数据加载成功:', data.length, '个省份');
        addressData = data;
        
        // 填充省份选项
        var $province = $('select[name="' + name + '[province]"]');
        $.each(data, function(index, province) {
            $province.append('<option value="' + province.name + '" data-id="' + province.id + '">' + province.name + '</option>');
            // 保存城市数据
            cityData[province.id] = province.children || [];
        });
        
        // 如果有已保存的地区数据，则回填
        if (selected && (selected.province || selected.city || selected.district)) {
            console.log('回填已保存的地区数据:', selected);
            doRenderProvince(selected.province || '', selected.city || '', selected.district || '', name);
        }
        
        layui.form.render('select');
        console.log('地区选择器初始化完成');
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('地址数据加载失败:', textStatus, errorThrown);
    });
    
    // 绑定事件监听
    layui.use(['form'], function(){
        var form = layui.form;
        
        // 省份选择事件
        form.on('select(provincePicker)', function (res) {
            console.log('省份选择事件触发:', res.value);
            updateRegionValue(name);
            doRenderCity("", "", name);
        });
        
        // 城市选择事件
        form.on('select(cityPicker)', function (res) {
            console.log('城市选择事件触发:', res.value);
            updateRegionValue(name);
            doRenderDist("", name);
        });
        
        // 区县选择事件
        form.on('select(districtPicker)', function (res) {
            console.log('区县选择事件触发:', res.value);
            updateRegionValue(name);
        });
    });
}

/**
 * 渲染省份选择
 */
function doRenderProvince(province="", city="", region="", name="data", Elem=null) {
    let picker = Elem ? $(Elem) : $('select[name="' + name + '[province]"]');
    picker.find('option:selected').removeProp("selected");
    if(province!==""){
        for (let i in addressData){
            if(addressData[i].name===province){
                let cur = addressData[i];
                picker.find('option[data-id="'+cur.id+'"]').prop("selected", true);
                break;
            }
        }
        doRenderCity(city, region, name, Elem);
    }else {
        $('select[name="' + name + '[city]"]').html('<option value="">城市</option>');
        $('select[name="' + name + '[region]"]').html('<option value="">区/县</option>');
        layui.form.render("select");
    }
}

/**
 * 渲染城市选择
 */
function doRenderCity(city="", region="", name="data", Elem=null){
    let options = '<option value="">城市</option>';
    let picker = Elem ? $(Elem) : $('select[name="' + name + '[province]"]');
    distData = {};
    
    // 获取选中的省份值
    let selectedProvince = picker.val();
    console.log('选中的省份:', selectedProvince);
    
    // 根据省份名称查找对应的ID
    let addressId = null;
    if (selectedProvince && addressData) {
        for (let i = 0; i < addressData.length; i++) {
            if (addressData[i].name === selectedProvince) {
                addressId = addressData[i].id;
                break;
            }
        }
    }
    
    console.log('省份ID:', addressId);
    if (addressId && cityData[addressId]){
        for (let i in cityData[addressId]){
            let cur = cityData[addressId][i];
            distData[cur.id] = cur.children || [];
            options += '<option value="'+cur.name+'" data-id="'+cur.id+'"'+(city===cur.name?' selected':'')+'>'+cur.name+'</option>';
        }
    }else{
        region = "";
    }
    $('select[name="' + name + '[city]"]').html(options);
    if(region!==""){
        doRenderDist(region, name);
    }else{
        $('select[name="' + name + '[region]"]').html('<option value="">区/县</option>');
        layui.form.render("select");
    }
}

/**
 * 渲染区县选择
 */
function doRenderDist(region="", name="data", Elem=null){
    let options = '<option value="">区/县</option>';
    let picker = Elem ? $(Elem) : $('select[name="' + name + '[city]"]');
    
    // 获取选中的城市值
    let selectedCity = picker.val();
    console.log('选中的城市:', selectedCity);
    
    // 获取选中的城市ID
    let cityId = picker.find('option:selected').data('id');
    console.log('城市ID:', cityId);
    
    if (cityId && distData[cityId]){
        for (let i in distData[cityId]){
            let dist = distData[cityId][i];
            options += '<option value="'+dist.name+'"'+(region===dist.name?' selected':'')+'>'+dist.name+'</option>';
        }
    }
    $('select[name="' + name + '[region]"]').html(options);
    layui.form.render("select");
}

/**
 * 更新地区值
 */
function updateRegionValue(name = "data") {
    var province = $('select[name="' + name + '[province]"]').val();
    var city = $('select[name="' + name + '[city]"]').val();
    var district = $('select[name="' + name + '[region]"]').val();
    
    // 优先使用区县，其次城市，最后省份
    var regionValue = district || city || province || '';
    console.log('更新地区值:', regionValue);
    
    // 触发自定义事件，供外部监听
    $(document).trigger('regionChanged', {
        name: name,
        province: province,
        city: city,
        district: district,
        value: regionValue
    });
} 