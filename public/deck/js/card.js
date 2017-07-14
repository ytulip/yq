var userVue = new Vue({
        el: "#app",
        data: {
            id: 1,
            name: "匿名",
            balance: 0,
            photo: "css/user.png",
            lostTemplate: ["你的人品貌似不佳哟，输了 {}", "诶，骚年，你需要继续努力，丢了 {}", "失去{}，对着天空大声吼，该出手时就出手呀。"]
        },
        computed: {
            fixedBalance: function() {
                return parseFloat(this.balance).toFixed(2);
            }
        },
    }),
    easing = Deck.easing,
    prefix = Deck.prefix,
    transform = prefix("transform"),
    transition = prefix("transition"),
    transitionDelay = prefix("transitionDelay"),
    boxShadow = prefix("boxShadow"),
    translate = Deck.translate,
    $container = document.getElementById("container"),
    $toolbar = document.getElementById("toolbar"),
    $poker = document.createElement("button");
$poker.textContent = "开始游戏";
$toolbar.appendChild($poker);
var deck = Deck(),
    acesClicked = [],
    kingsClicked = [];
deck.cards.forEach(function(a, b) {
    function c() {
        var a;
        if (0 === b % 13) acesClicked[b] = !0, 4 === acesClicked.filter(function(a) {
            return a
        }).length && (document.body.removeChild($toolbar), deck.$el.style.display = "none", setTimeout(function() {
            startWinning()
        }, 250));
        else if (12 === b % 13) {
            if (kingsClicked && (kingsClicked[b] = !0, 4 === kingsClicked.filter(function(a) {
                    return a
                }).length)) {
                for (var c = 0; 3 > c; c++) a = Deck.Card(52 + c), a.mount(deck.$el), a.$el.style[transform] = "scale(0)", a.enableMoving(), deck.cards.push(a);
                deck.sort(!0);
                kingsClicked = !1
            }
        } else acesClicked = [], kingsClicked && (kingsClicked = [])
    }
    a.$el.addEventListener("mousedown", c);
    a.$el.addEventListener("touchstart", c);
    a.$el.card = a
});
var betMoney = 0,
    totalBetMoney = 0,
    betNumber = 0,
    leftNumber = 0,
    turnCarding = !1,
    currentMoney = 0,
    prevBetMoney = 0,
    isFourCard = !1,
    gameRunning = !1;

function onTurnCard(a) {
    if (!(turnCarding || 0 >= leftNumber)) {
        turnCarding = !0;
        var b = this,
            c = a.target;
        b.removeEventListener("mousedown", onTurnCard);
        b.removeEventListener("touchend", onTurnCard);
        c.classList.remove("back");
        turnCard(c.parentNode, function() {
            c.parentNode.classList.add(b.card.suitName, b.card.suitNameValue);
            b.card.$el.getElementsByClassName("topleft")[0].innerHTML = b.card.value;
            b.card.$el.getElementsByClassName("bottomright")[0].innerHTML = b.card.value
        }, function() {
            turnCarding = !1;
            --leftNumber;
            console.log(b.card.value);
            switch (b.card.value) {
                case 1:
                    currentMoney += 2 * betMoney;
                    break;
                case 2:
                    currentMoney += 1.5 * betMoney;
                    break;
                case 3:
                    currentMoney += betMoney;
                    break;
                case 4:
                    isFourCard = !0, console.log("下一局享受下注一张减半")
            }
            if (0 < leftNumber) {
                var a = printMessage("您还有" + leftNumber + "张牌可以翻哟~", 200, "tip");
                document.body.removeChild(a)
            } else 0 == leftNumber && (userVue.balance = currentMoney, 0 < currentMoney - totalBetMoney ? isFourCard ? $("#contentDiv").text("恭喜你赢了 " + (currentMoney - totalBetMoney) + "，还中了四等奖，下次同等下注金额可减半但享受同等金额的奖励哦，妥妥哒~") : $("#contentDiv").text("恭喜你赢了 " + (currentMoney - totalBetMoney) + "，妥妥哒~") : 0 == currentMoney - totalBetMoney ? isFourCard ? $("#contentDiv").text("呃，你只中了四等奖，下次同等金额首注减半但享受同等金额的奖励哦~") : $("#contentDiv").text("呃，这次没有进账也没有损失哟~") : isFourCard ? $("#contentDiv").text("呃，你损失了" + (currentMoney - totalBetMoney) + "，但中了四等奖，下次同等金额首注减半但享受同等金额的奖励哦~") : (a = getRandomNum(userVue.lostTemplate.length - 1), $("#contentDiv").text(userVue.lostTemplate[a].replace("{}", currentMoney - totalBetMoney))), prevBetMoney = betMoney, document.getElementById("modal2").classList.add("md-show"), $poker.removeAttribute("disabled"), gameRunning = !1, updateUseMoney(userVue.id, userVue.balance))
        })
    }
}

function turnCard(a, b, c) {
    var d = a.getAttribute("rotateY");
    d || (d = "0");
    a.setAttribute("rotateY", "0" == d ? "-180" : "0");
    a.getAttribute("transform") || a.setAttribute("transform", a.style.transform);
    a.style.transition = "";
    montion(a, "200ms", function() {
        this.style.transform = this.getAttribute("transform") + " scale(0)"
    }, function() {
        b();
        montion(this, "500ms", function() {
            this.style.transform = this.getAttribute("transform") + " scale(1)"
        }, c)
    })
}

function montion(a, b, c, d) {
    a.style.transition = b;
    c.call(a);
    var e = !1;
    a.addEventListener("transitionend", function() {
        e || (d && d.call(a), e = !0)
    }, !1)
}
$poker.addEventListener("click", function() {
    deck.cards.forEach(function(a, b) {
        a.$el.removeAttribute("transform");
        a.$el.classList.remove(a.suitName, a.suitNameValue);
        a.$el.classList.add("back");
        a.$el.getElementsByClassName("topleft")[0].innerHTML = "";
        a.$el.getElementsByClassName("bottomright")[0].innerHTML = "";
        $poker.setAttribute("disabled", "disabled")
    });
    deck.shuffle();
    deck.shuffle();
    deck.shuffle();
    deck.poker();
    setTimeout(function() {
        document.getElementById("modal").classList.add("md-show");
        isFourCard && $("#selectMoneyDiv .btn").each(function() {
            $(this).text() == prevBetMoney ? $(this).addClass("select") : $(this).removeClass("select")
        })
    }, 3200);
    [].slice.call(document.querySelectorAll(".message")).forEach(function(a, b) {
        a.style[transform] = translate(-window.innerWidth + "px", 0)
    })
});
deck.mount($container);
$poker.setAttribute("disabled", "disabled");
deck.intro();
deck.fan();
setTimeout(function() {
    $poker.removeAttribute("disabled")
}, 2E3);

function startTip() {
    var a = 1E4 + 6E4 * Math.random();
    setTimeout(function() {
        gameRunning || printMessage("翻中A返回2倍金额哟...")
    }, a);
    setTimeout(function() {
        gameRunning || printMessage("游戏好玩，可不要过于沉溺哟...")
    }, a + 5E3);
    setTimeout(function() {
        gameRunning || printMessage("...祝各位好运 ;)")
    }, a + 1E4)
}

function printMessage(a, b, c) {
    c || (c = "message");
    var d = document.createElement("p");
    d.classList.add(c);
    d.textContent = a;
    document.body.appendChild(d);
    d.style[transform] = translate(window.innerWidth + "px", 0);
    b || (b = 1E3);
    setTimeout(function() {
        d.style[transition] = "all .7s " + easing("cubicInOut");
        d.style[transform] = translate(0, 0)
    }, b);
    setTimeout(function() {
        d.style[transform] = translate(-window.innerWidth + "px", 0)
    }, 4E3);
    setTimeout(function() {
        document.body.removeChild(d)
    }, 5E3);
    return d
}

function startGame() {
    if (betMoney = getSelectMoney()) if (betNumber = getSelectCardNumber()) document.getElementById("modal").classList.remove("md-show"), currentMoney = 0, leftNumber = betNumber, totalBetMoney = betMoney * betNumber, isFourCard && betMoney != prevBetMoney || (isFourCard && (totalBetMoney = betMoney / 2 + betMoney * (betNumber - 1)), 0 > userVue.balance - totalBetMoney ? ($("#contentDiv").text("你的余额已经不足" + totalBetMoney + "，请先充值吧~"), document.getElementById("modal2").classList.add("md-show"), $poker.removeAttribute("disabled")) : (printMessage("请开始翻牌吧，祝君好运！", 200, "tip"), userVue.balance -= totalBetMoney, gameRunning = !0, isFourCard = !1, deck.cards.forEach(function(a, b) {
        a.$el.addEventListener("mousedown", onTurnCard);
        a.$el.addEventListener("touchend", onTurnCard)
    }), $poker.setAttribute("disabled", "disabled"), $("#selectCardDiv .btn.select").removeClass("select"), $("#selectMoneyDiv .btn.select").removeClass("select"), [].slice.call(document.querySelectorAll(".message")).forEach(function(a, b) {
        a.style[transform] = translate(-window.innerWidth + "px", 0)
    })))
}
var selectFx;
$(function() {
    $("#selectMoneyDiv .btn").click(function() {
        $(this).siblings(".select").removeClass("select");
        $(this).addClass("select");
        selectFx.selPlaceholder.textContent = "10 - 100 自选";
        selectFx.current = -1;
        parseInt($(this).text()) != prevBetMoney && isFourCard ? $("#selectTitle").text("不享受四等奖优惠") : isFourCard ? $("#selectTitle").text("首注金额减半") : $("#selectTitle").text("投注")
    });
    $("#selectCardDiv .btn").click(function() {
        $(this).siblings(".select").removeClass("select");
        $(this).addClass("select");
        "首注金额减半" != $("#selectTitle").text() && $("#selectTitle").text("投注")
    });
    document.getElementById("md-overlay").addEventListener("click", function() {
        document.getElementById("modal").classList.remove("md-show");
        $poker.removeAttribute("disabled")
    });
    selectFx = new SelectFx(document.getElementById("otherSelect"), {
        stickyPlaceholder: !1,
        onChange: function(a) {
            $("#selectMoneyDiv .btn.select").removeClass("select");
            parseInt(a) != prevBetMoney && isFourCard ? $("#selectTitle").text("不享受四等奖优惠") : isFourCard ? $("#selectTitle").text("首注金额减半") : $("#selectTitle").text("投注");
            return !1
        }
    });
    $("#preLoad").remove();
    startTip();
    initData()
});

function getSelectMoney() {
    var a = $("#selectMoneyDiv .btn.select").text();
    a || -1 == selectFx.current || (a = selectFx.el.value);
    if (a && "" != a) return parseInt(a);
    $("#selectTitle").text("请选择金额")
}

function getSelectCardNumber() {
    var a = $("#selectCardDiv .btn.select").text();
    if (a && "" != a) return parseInt(a);
    $("#selectTitle").text("请选择翻卡张数")
}

function restart() {
    document.getElementById("modal2").classList.remove("md-show");
    deck.cards.forEach(function(a, b) {
        a.$el.removeAttribute("transform");
        a.$el.classList.add(a.suitName, a.suitNameValue);
        a.$el.classList.remove("back");
        a.$el.getElementsByClassName("topleft")[0].innerHTML = a.value;
        a.$el.getElementsByClassName("bottomright")[0].innerHTML = a.value
    });
    deck.fan()
}
function getRandomNum(a) {
    a || (a = 100);
    return Math.ceil(Math.random() * a)
};