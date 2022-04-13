// 漢堡條效果
$('.burger').on('click', (e) => {
    $(e.currentTarget).toggleClass('burger_active')
    $('.mobile_nav_list').toggleClass('nav_active')
})

// 點擊漢堡條 跳出選單
$('.burger').on('click', () => {
    $('.mobile_right').toggleClass('active')
})

new fullpage('#fullpage', {
    sectionsColor: [],
});


