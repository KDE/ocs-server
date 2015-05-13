<?php

include("gfx3/lib.php");

EStructure::load("gamelist");

EStructure::code();

?>


<div class="span6">
<h2>Gaming What?</h2>

<p>The freedom of both the creators of games and the players of games have been slowly disappearing for a long time, with DRM and everything else making life more difficult for everybody, except the pirates. The first steps towards a more free world of gaming experiences came in 2008 when The Gamer's Bill of Rights was introduced at PAX in 2008.</p>

<p>Since then, there has been much discussion and activity, and it is the belief of the Gluon team that the world is ready for something new: The freedom of gaming taking to a new level. We propose a new method of game development and distribution, which brings the creators of games and the players of games together.</p>

<h2>Define Freedom?</h2>

<p>The Gluon project is building a game engine and distribution system, which allows the creators of games to build games easily and collaboratively, powered by open technologies such as Qt, OpenGL, OpenAL, Git and many more. All of this is wrapped up in a nice, easy to use tool called Gluon Creator, from where you can publish games directly onto the GamingFreedom.org website.</p>

<p>On the website, the players of games can discuss and compete with each other, and with the creators of games, who have an instant community for the games they publish.</p>

<h2>How Free Must I Be?</h2>

<p>Not everybody in the game development world is equally happy with opening up their work to the world, and as such, the games distributed through GamingFreedom.org can be any of the Creative Commons licenses.</p>

<p>What this means is that you have a choice in how free you want your work to be - all the way from the classic public domain license, allowing everybody to do anything they want with what you have made, all the way to allowing people to only redistribute the work you have created as-is, with no changes at all.</p>

<h2>What About My Inner Capitalist?</h2>

<p>People must of course be able to survive in a world in which the movement of little pieces of green paper supposedly define happiness. As such, GamingFreedom.org provides many ways for the players of games to give back to the creators of the games they enjoy. While games are free to download, the players of games are encouraged to donate to the makers through a wide variety of avenues:</p>

<p>Supporting both PayPal and Nokia Money, as well as the new kid on the micropayment block Flattr, GamingFreedom.org gives the players of games every opportunity to support the creators of games by giving them money, and thus encourage them to continue creating amazing games.</p>
</div>

<?php
EStructure::insert("game_list");

EStructure::unload();

?>
