
# [PixelPals/PixelPals-Auth]

> ⚠️ **Status: Archived / Unmaintained**  
> This is a legacy PHP project made for <a href="https://github.com/pmmp/PocketMine-MP">PMMP 3.0.0</a> (MCPE 0.15.10) and preserved for historical purposes only. It is no longer actively developed, updated, or monitored for security vulnerabilities.

</br>

<h2 align="center"><span>About</span></h2>
<p>This is a simple and lightweight auth plugin which uses simple XOR comparator based encryption method with a string key for security and this plugin was made for old PixelPals server to be compatible with other server plugins<br>It features fast IP based login and chat based registering to keep things easy for new users, you just type your password into the chat to register.</p>

</br>

<h2 align="center"><span>Commands</span></h2>

```
/changepassword <newPassword>
/chpass <newPassword>
```

> Can only be used by the player to change their password and the required permission is __ch.pass__, minimum allowed password length is 6

</br>

```
/respass <player> <newPassword>
```

> Can be used to change other players' passwords, this command is dangerous, only authorized people can use it, and the required permission is __res.pass__

</br>

```
/seepass <player>
```

> Can be used to see other players' passwords by pulling encrypted data from the file, it does not show the password itself, this command can only be used by authorized people, and the required permission is __see.pass__

</br>

<h2 align="center"><span>Permissions</span></h2>

- **`ch.pass`**
  - Default permission, everyone has it
- **`res.pass`**
  - Admin permission
- **`see.pass`**
  - Admin permission

</br>

<h2 align="center"><span>File activity</span></h2>

When enabled, the plugin will create a YAML file: <b>~/PP_Auth/accounts.pp</b>


</br>

<h2 align="center"><span>Affected events</span></h2>

- PlayerQuitEvent
- PlayerJoinEvent
- PlayerChatEvent
- PlayerMoveEvent
- PlayerInteractEvent
- PlayerToggleSprintEvent
- PlayerDropItemEvent
- PlayerCommandPreprocessEvent
- BlockBreakEvent

</br>
</br>
</br>

<p align="center"><img src="https://media.tenor.com/o9rNU1uX_R0AAAAj/cat-campfire.gif" alt="cats" width="200"/></p>
<p align="center">
  <sub>Made with <b>0.6% AI</b> / <b>99.4% Human Code & Passion</b></sub><br>
  <sub><i>Preserved with pride from the PixelPals era.</i></sub>
</p>
