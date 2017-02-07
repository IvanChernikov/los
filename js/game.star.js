$.ready = function () {
    engine.Init(new Config("GameWindow",800,600,false,30,'star'));
    engine.LoadImage('background','space.jpg');
    engine.LoadImage('player','star.jpg');
    engine.LoadImage('trail','star_trail.jpg');
    engine.LoadImage('star','star_200px.jpg');
    engine.LoadImage('glow','glow_79px.bmp');
    engine.Start();
}
