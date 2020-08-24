# Home Automation AC Controls

This project aims to create a Wi-Fi enabled IR emitter (_Controller_) to control
air conditioning units.

The AC units settings will be stored and updated via a web-service (see `/server`).

## API

Enumerable `mode` - (cold: 1, hot: 2, dry: 3, vent: 4)

- **GET /api.php** - returns URL-encoded list of current ACs' settings
- **GET /api.php?name=AAA** - returns the settings of the unit with `name` `AAA` as a string of values,
in the following order `name;mode;temp;speed;status;update`, e.g.:
  > `LVN;1;20;2;1;1`

  meaning the AC unit name is `LVN`, its mode is _cold_, temperature 20°C, air speed 2, status is ON, and update is a special field for the _Controller_ to determine if it's a new setting
- **POST /api.php** - update the settings for unit. Should contain data in form-urlencoded
format with the following keys:
  - `name` - _string_ -  up to 16 characters
  - `mode` - _int_ - _see above_
  - `temp` - _int_ - temperature in degrees Celcius
  - `speed` - _int_ - fan speed (1-3)
  - `status` - _int_ - on or off (1 or 0)
