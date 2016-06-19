$('.todo-list li').each(function () {
    var $this = $(this);
    var $input = $this.find('input:text');

    $this.data('value', $input.val());

    $this.dblclick(function () {
        $input.val($this.data('value'));
        $this.addClass('editing');
        $input.focus();
    });
    $input.blur(function () {
        $this.removeClass('editing');
    });
    $input.keyup(function (e) {
        if (e.keyCode === 27) {
            $this.removeClass('editing');
        }
    });
});

$('.flash').delay(1000).fadeTo(1000, 0);
