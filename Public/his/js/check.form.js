/*****************************************************************
                  表单校验工具类  (malixiao)       
*****************************************************************/
 
/**
 * 判断整数num是否等于0
 * 
 * @param num
 * @return
 * @author malixiao
 */
function isIntEqZero(num){ 
     return num==0;
}

/**
 * 判断整数num是否大于0
 * 
 * @param num
 * @return
 * @author malixiao
 */
function isIntGtZero(num){ 
    return num>0;
}

/**
 * 判断整数num是否大于或等于0
 * 
 * @param num
 * @return
 * @author malixiao
 */
function isIntGteZero(num){ 
    return num>=0;
}

/**
 * 判断浮点数num是否等于0
 * 
 * @param num 浮点数
 * @return
 * @author malixiao
 */
function isFloatEqZero(num){ 
    return num==0;
}

/**
 * 判断浮点数num是否大于0
 * 
 * @param num 浮点数
 * @return
 * @author malixiao
 */
function isFloatGtZero(num){ 
    return num>0;
}

/**
 * 判断浮点数num是否大于或等于0
 * 
 * @param num 浮点数
 * @return
 * @author malixiao
 */
function isFloatGteZero(num){ 
    return num>=0;
}

/**
 * 匹配Email地址
 */
function isEmail(str){
    if(str==null||str=="") return false;
    var result=str.match(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/);
    if(result==null)return false;
    return true;
}

/**
 * 判断数值类型，包括整数和浮点数
 */
function isNumber(str){
    if(isDouble(str) || isInteger(str)) return true;
    return false;
}     

/**
 * 只能输入数字[0-9]
 */
function isDigits(str){
    if(str==null||str=="") return false;
    var result=str.match(/^\d+$/);
    if(result==null)return false;
    return true;
}     

/**
 * 匹配money
 */
function isMoney(str){
    if(str==null||str=="") return false;
    var result=str.match(/^(([1-9]\d*)|(([0-9]{1}|[1-9]+)\.[0-9]{1,2}))$/);
    if(result==null)return false;
    return true;
} 
    
/**
 * 匹配phone
 */
function isPhone(str){
    if(str==null||str=="") return false;
    var result=str.match(/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/);
    if(result==null)return false;
    return true;
}     

/**
 * 匹配mobile
 */
function isMobile(str){
    if(str==null||str=="") return false;
    var result=str.match(/^((\(\d{2,3}\))|(\d{3}\-))?((13\d{9})|(15\d{9})|(18\d{9}))$/);
    if(result==null)return false;
    return true;
}     

/**
 * 联系电话(手机/电话皆可)验证   
 */
function isTel(text){
    if(isMobile(text)||isPhone(text)) return true;
    return false;
}

/**
 * 匹配qq
 */
function isQq(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[1-9]\d{4,12}$/);
    if(result==null)return false;
    return true;
}     

/**
 * 匹配english
 */
function isEnglish(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[A-Za-z]+$/);
    if(result==null)return false;
    return true;
}     

/**
 * 匹配integer
 */
function isInteger(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[-\+]?\d+$/);
    if(result==null)return false;
    return true;
}     

/**
 * 匹配double或float
 */
function isDouble(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[-\+]?\d+(\.\d+)?$/);
    if(result==null)return false;
    return true;
}     


/**
 * 匹配邮政编码
 */
function isZipCode(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[0-9]{6}$/);
    if(result==null)return false;
    return true;
} 

/**
 * 匹配URL
 */
function isUrl(str){
    if(str==null||str=="") return false;
    var result=str.match(/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/);
    if(result==null)return false;
    return true;
} 

/**
 * 匹配密码，以字母开头，长度在6-12之间，只能包含字符、数字和下划线。
 */
function isPwd(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[a-zA-Z]\\w{6,12}$/);
    if(result==null)return false;
    return true;
} 

/**
 * 判断是否为合法字符(a-zA-Z0-9-_)
 */
function isRightfulString(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[A-Za-z0-9_-]+$/);
    if(result==null)return false;
    return true;
} 

/**
 * 匹配english
 */
function isEnglish(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[A-Za-z]+$/);
    if(result==null)return false;
    return true;
} 

/**
 * 匹配身份证号码
 */
function isIdCardNo(num){ 
　   //　 if (isNaN(num)) {alert("输入的不是数字！"); return false;} 
　　 var len = num.length, re; 
　　 if (len == 15) 
　　 re = new RegExp(/^(\d{6})()?(\d{2})(\d{2})(\d{2})(\d{2})(\w)$/); 
　　 else if (len == 18) 
　　 re = new RegExp(/^(\d{6})()?(\d{4})(\d{2})(\d{2})(\d{3})(\w)$/); 
　　 else {/*alert("输入的数字位数不对。");*/ return false;} 
　　 var a = num.match(re); 
　　 if (a != null) 
　　 { 
　　 if (len==15) 
　　 { 
　　 var D = new Date("19"+a[3]+"/"+a[4]+"/"+a[5]); 
　　 var B = D.getYear()==a[3]&&(D.getMonth()+1)==a[4]&&D.getDate()==a[5]; 
　　 } 
　　 else 
　　 { 
　　 var D = new Date(a[3]+"/"+a[4]+"/"+a[5]); 
　　 var B = D.getFullYear()==a[3]&&(D.getMonth()+1)==a[4]&&D.getDate()==a[5]; 
　　 } 
　　 if (!B) {/*alert("输入的身份证号 "+ a[0] +" 里出生日期不对。");*/ return false;} 
　　 } 
　　 if(!re.test(num)){/*alert("身份证最后一位只能是数字和字母。");*/return false;}
　　  
　　 return true; 
} 

/**
 * 匹配汉字
 */
function isChinese(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[\u4e00-\u9fa5]+$/);
    if(result==null)return false;
    return true;
} 

/**
 * 匹配中文(包括汉字和字符)
 */
function isChineseChar(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[\u0391-\uFFE5]+$/);
    if(result==null)return false;
    return true;
}     

/**
 * 字符验证，只能包含中文、英文、数字、下划线等字符。
 */
function stringCheck(str){
    if(str==null||str=="") return false;
    var result=str.match(/^[a-zA-Z0-9\u4e00-\u9fa5-_]+$/);
    if(result==null)return false;
    return true;
}     

/**
 * 过滤中英文特殊字符，除英文"-_"字符外
 */
function stringFilter(str){
    var pattern = new RegExp("[`~!@#$%^&*()+=|{}':;',\\[\\].<>/?~！@#￥%……&*（）——+|{}【】‘；：”“’。，、？]");
    var rs = "";
    for (var i = 0; i < str.length; i++) {
        rs = rs + str.substr(i, 1).replace(pattern, '');
    }
    return rs;
} 

/**
 * 判断是否包含中英文特殊字符，除英文"-_"字符外
 */
function isContainsSpecialChar(str){
    if(str==null||str=="") return false;
    var reg = RegExp(/[(\ )(\`)(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\+)(\=)(\|)(\{)(\})(\')(\:)(\;)(\')(',)(\[)(\])(\.)(\<)(\>)(\/)(\?)(\~)(\！)(\@)(\#)(\￥)(\%)(\…)(\&)(\*)(\（)(\）)(\—)(\+)(\|)(\{)(\})(\【)(\】)(\‘)(\；)(\：)(\”)(\“)(\’)(\。)(\，)(\、)(\？)]+/);   
    return reg.test(str);    
}

/**
 * 判断密码必须为数字或字母，或数字字母组合,并且为6-8个字符
 * gmq
 */
function is_password(str){
 var re = /^[0-9a-zA-Z]{6,8}$/;
 if(!re.test(str)){
     return false;
 }
 return true;


}
/*
 根据〖中华人民共和国国家标准 GB 11643-1999〗中有关公民身份号码的规定，公民身份号码是特征组合码，由十七位数字本体码和一位数字校验码组成。排列顺序从左至右依次为：六位数字地址码，八位数字出生日期码，三位数字顺序码和一位数字校验码。
 地址码表示编码对象常住户口所在县(市、旗、区)的行政区划代码。
 出生日期码表示编码对象出生的年、月、日，其中年份用四位数字表示，年、月、日之间不用分隔符。
 顺序码表示同一地址码所标识的区域范围内，对同年、月、日出生的人员编定的顺序号。顺序码的奇数分给男性，偶数分给女性。
 校验码是根据前面十七位数字码，按照ISO 7064:1983.MOD 11-2校验码计算出来的检验码。

 出生日期计算方法。
 15位的身份证编码首先把出生年扩展为4位，简单的就是增加一个19或18,这样就包含了所有1800-1999年出生的人;
 2000年后出生的肯定都是18位的了没有这个烦恼，至于1800年前出生的,那啥那时应该还没身份证号这个东东，⊙﹏⊙b汗...
 下面是正则表达式:
 出生日期1800-2099  (18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])
 身份证正则表达式 /^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i
 15位校验规则 6位地址编码+6位出生日期+3位顺序号
 18位校验规则 6位地址编码+8位出生日期+3位顺序号+1位校验位

 校验位规则     公式:∑(ai×Wi)(mod 11)……………………………………(1)
 公式(1)中：
 i----表示号码字符从由至左包括校验码在内的位置序号；
 ai----表示第i位置上的号码字符值；
 Wi----示第i位置上的加权因子，其数值依据公式Wi=2^(n-1）(mod 11)计算得出。
 i 18 17 16 15 14 13 12 11 10 9 8 7 6 5 4 3 2 1
 Wi 7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2 1

 */
//身份证号合法性验证
//支持15位和18位身份证号
//支持地址编码、出生日期、校验位验证
function IdentityCodeValid(code) {
    var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
    var tip = "";
    var pass= true;

    if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i.test(code)){
        tip = "身份证号格式错误";
        pass = false;
    }

    else if(!city[code.substr(0,2)]){
        tip = "地址编码错误";
        pass = false;
    }
    else{
        //18位身份证需要验证最后一位校验位
        if(code.length == 18){
            code = code.split('');
            //∑(ai×Wi)(mod 11)
            //加权因子
            var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
            //校验位
            var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
            var sum = 0;
            var ai = 0;
            var wi = 0;
            for (var i = 0; i < 17; i++)
            {
                ai = code[i];
                wi = factor[i];
                sum += ai * wi;
            }
            var last = parity[sum % 11];
            if(parity[sum % 11] != code[17]){
                tip = "校验位错误";
                pass =false;
            }
        }
    }
    if(!pass) remindBox(tip);
    return pass;
}
