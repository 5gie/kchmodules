const closeExtraFields = ($this, rm = false) => {

    $this.hide();

    if($(window).width() > 767){
        $this.siblings('.text').fadeOut(500, () => {
            
            setTimeout(() => {
    
                $this.closest('.col').removeClass('active');
                $('#extra_fields .col').not('.active').find('h3').addClass('hidden');
                if(rm) $('#extra_fields').removeClass('animate');
    
            }, 100);
    
        });

    } else {

        $this.siblings('.text').find('.content').removeClass('active');
        setTimeout(() => {
    
            $this.siblings('.text').slideUp(700, () => {
                
                setTimeout(() => {
        
                    $this.closest('.col').removeClass('active');
                    $('#extra_fields .col').not('.active').find('h3').addClass('hidden');
                    if(rm) $('#extra_fields').removeClass('animate');
        
                }, 100);
        
            });
    
        },200)
        
    }
    

}

const openExtraFields = ($this) => {
    
    $this.closest('.col').addClass('active');

    if($(window).width() > 767){

        setTimeout(() => {
            $this.siblings('.close').show();
            $this.siblings('.text').fadeIn(500, function() {

                $('#extra_fields').removeClass('animate');

            });
            
        },700);
        
    }else {

        $this.siblings('.close').show();
        $this.siblings('.text').slideDown(700, function() {

            $this.siblings('.text').find('.content').addClass('active');

            $('#extra_fields').removeClass('animate');

        });
            
    }

}

$(document).ready(() => {

    $('body').on('click', '#extra_fields:not(".animate") .col .open', function () {

        $('#extra_fields').addClass('animate');

        if ($('#extra_fields').hasClass('is-active')) {

            $.when(closeExtraFields($('#extra_fields').find('.col.active .close'))).then(() => {

                setTimeout(() => {

                    openExtraFields($(this));

                }, 1200);

            });


        } else {

            openExtraFields($(this));
            $('#extra_fields').toggleClass('is-active');
            $('#extra_fields .col').not('.active').find('h3').addClass('hidden');

        }

    });

    $('body').on('click', '#extra_fields:not(".animate") .col .close', function () {

        $('#extra_fields').addClass('animate');
        closeExtraFields($(this), true);
        $('#extra_fields').toggleClass('is-active');
        setTimeout(() => {
            $('#extra_fields .col').not('.active').find('h3').removeClass('hidden');
        }, 1000);

    });

})
