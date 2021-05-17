jQuery(document).ready(function($){
    $('.cfb-flip').each(function(){ 
        
        $(this).on('touchstart', function(){
        $(this).flip('toggle'); 
        }); 
    
	}); 
    $('.cfb_wrapper').each(function(){
        
        var flipboxID=$(this).data('flipboxid');
		var cfb_flip = $(this).find('.cfb-flip');
        var effect = cfb_flip.data('effect');

        cfb_flip.flip({
        axis:effect,
        trigger: 'hover',
        front:'.flipbox-front-layout',
        back:'.flipbox-back-layout',
        autoSize:false
        });
    /* 
        cfb_flip.on('touchstart', function(){
            cfb_flip.flip('toggle'); 
        });  */
        
        $('.cfb-data a').on('touchstart',function(e){
            e.stopPropagation();
        })

        $(this).imagesLoaded(function() {
                     
           var maxDataHeight = 0;
           
            $('#'+flipboxID+' '+'.cfb-flip[data-height="equal"]'+' '+'.cfb-data').each(function(){
               if ($(this).outerHeight() > maxDataHeight) { 
                   maxDataHeight = $(this).outerHeight();                
                }
            });
            
            $('#'+flipboxID+' '+'.cfb-flip[data-height="equal"]'+' '+'.cfb-data').outerHeight(maxDataHeight);
            
        });
    
    });
    
    
    var maxHeight = 0;
    $('.cfb_wrapper').imagesLoaded(function() {
        $('.cfb-box-wrapper').each(function(){
            var $this = $(this);

            var frontHeight = $this.find('.flipbox-front-layout').outerHeight();
            var backHeight =  $this.find('.flipbox-back-layout').outerHeight();
            
            if (frontHeight > backHeight) { maxHeight = frontHeight; }
            else{
             maxHeight = backHeight;
            }       
          
            $this.find('.cfb-data').outerHeight(maxHeight); 
        
        });
    });


 
});

