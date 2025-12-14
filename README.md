# Survival
This is a plugin focused on combining the use of different utilities available on a survival server (/sethome, /home, tpa...).

> Worlds defined as survival will be referred to as worlds with the survival property.

	
> [!IMPORTANT]
> This plugin is dependent on [Smartcommand](https://github.com/RajadorDev/SmartCommand/tree/pm-2.0.0).

## Exclusive utilities
As a unique feature, survival mode allows players to save their items even when switching worlds, and it's possible to hold events in a way that prevents players from losing their items (for now, this can only be done by holding events in worlds other than the one defined as survival).


## Admin Commands
- `/survival set <world>` The world is set to survival mode; if no world is mentioned, the world the player is in will be set as the target.
- `/survival remove <world>` Remove a world defined as survivors; if no world is mentioned, the current world will be defined as target.
- `/survival list` List all worlds with the survival property.

## Player Commands
> More commands will be added in the future.

> [!NOTE]
> Homes can only be defined and used in survival worlds, and the `/home list` will be displayed according to the world the player is in.
- `/home <home>` Lists all the player's home and displays other home-related commands. If the name of an home is mentioned, the player will be teleported to their sajdasdas with that name.
- `/home set <home>` Define a player's home.
- `/home del <home>` Remove a home with the mentioned name.
