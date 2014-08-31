Ext.Loader.setConfig({
    enabled : true,
    paths   : {
        lib : "assets/lib"
    }
});

var mytest = Ext.create('lib.test', 'test');      
console.log ("test: " + mytest.name );

// Ext.application({
    // name: 'HelloExt',
    // launch: function() {
        // Ext.create('Ext.container.Viewport', {
            // layout: 'fit',
            // items: [
                // {
                    // title: 'Hello Ext',
                    // html : 'Hello! Welcome to Ext JS.'
                // }
            // ]
        // });
    // }
// });