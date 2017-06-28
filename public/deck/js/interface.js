/**
 * 初始化数据
 */
function initData(){
    var userObj = {};
    // userVue.id = 1;
    $.ajax({
        type : "get",
        url : "/info",
        data : {},
        async : false,
        dataType:'json',
        success : function(data){
            if ( data.status )
            {
                userObj = data.data;
            } else {
                alert(data.error);
            }
        }
    });
    userVue.id = userObj.id;
    userVue.name = userObj.id;
    userVue.balance = userObj.charge;
    document.getElementById("photoImg").src = 'css/user.png';
    //金额损失的模板
    userVue.lostTemplate = ["你的人品貌似不佳哟，输了 {}",
            "诶，骚年，你需要继续努力，丢了 {}",
            "失去{}，对着天空大声吼，该出手时就出手呀。"];
}

/**
 * 这里更新用户金额到服务器
 * @param userId 用户的id
 * @param money 当前金额，直接更新，不用计算
 */
function updateUseMoney(userId, money){
    //TODO：确定这里不会有问题,第一次的时候不会去调用这个函数

    $.ajax({
        type : "post",
        url : "/card-do",
        data : {money:money},
        async : false,
        dataType:'json',
        success : function(data){
            if ( data.status )
            {
                console.log(data.data);
            } else {
                alert(data.error);
            }
        }
    });
}