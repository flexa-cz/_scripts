 <?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * PEAR::Science_Astronomy
 *
 * This class implements various astronomical algorithms, which have a high
 * accuracy to calculate Sun and Moon events using Earth as the viewpoint.
 * Also included are standard calculations that are typically used in
 * Astronomy, such as sidereal time and julian day count.
 *
 * o calculateSunRiseSet
 *   Calculates the sunrise and sunset for a location on a given day, including
 *   the azimuths. Describes correct behaviour in Arctic and Antarctic regions,
 *   where the sun may not rise or set on the date.
 * o calculateMoonRiseSet
 *   Calculates the moonrise and moonset for a location on a given day,
 *   including the azimuths. Describes correct behaviour in Arctic and
 *   Antarctic regions, where the moon may not rise or set on the date.
 * o calculateMoonPhase
 *   Calculates the moon's phase (age), distance, and position along the
 *   ecliptic on a given day within several thousand years in the past or
 *   future.
 * o meanSiderealTime
 *   Calculate the Mean Sidereal Time. If you leave out the longitude and
 *   the timezone, you'll get the Greenwich Mean Sidereal Time or GMST.
 * o dateToJD
 *   Calculate the Julian Day Number (JD) for a given date and time, including
 *   the decimal places for hours, minutes and seconds.
 *
 * PHP versions 4 and 5
 *
 * <LICENSE>
 * Copyright (c) 2011, Alexander Wirtz
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * o Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * o Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * o Neither the name of the software nor the names of its contributors
 *   may be used to endorse or promote products derived from this software
 *   without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * </LICENSE>
 *
 * @category    Science
 * @package     Science_Astronomy
 * @author      Alexander Wirtz <eru@php.net>
 * @copyright   2011 Alexander Wirtz
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version     SVN: $Id:
 * @link        http://pear.php.net/package/Science_Astronomy
 * @filesource
 */

// {{{ constants
// {{{ error codes
define("SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR",   0);
define("SCIENCE_ASTRONOMY_ERROR_DATE_INVALID",    1);
define("SCIENCE_ASTRONOMY_ERROR_RETFORM_INVALID", 2);
// }}}

// {{{ return formats
define("SCIENCE_ASTRONOMY_RET_TIMESTAMP", 0);
define("SCIENCE_ASTRONOMY_RET_STRING",    1);
define("SCIENCE_ASTRONOMY_RET_DOUBLE",    2);
// }}}

// {{{ default values
define("SCIENCE_ASTRONOMY_DEFAULT_LATITUDE",  0);
define("SCIENCE_ASTRONOMY_DEFAULT_LONGITUDE", 0);
// }}}

// {{{ predefined constants
define("DR", M_PI / 180);
define("K1", 15 * DR * 1.0027379);
// }}}
// }}}

// {{{ class Science_Astronomy
/**
 * This class provides functions to calculate various astronomical algorithms
 *
 * @category    Science
 * @package     Science_Astronomy
 * @author      Alexander Wirtz <eru@php.net>
 * @copyright   2011 Alexander Wirtz
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version     Release: @package_version@
 * @link        http://pear.php.net/package/Science_Astronomy
 */
class Science_Astronomy {
    // {{{ apiVersion()
    /**
     * For your convenience, when I come up with changes in the API...
     *
     * @return  string
     * @access  public
     */
    function apiVersion()
    {
        return "0.1";
    }
    // }}}

    // {{{ _errorMessage()
    /**
     * Returns the message for a certain error code
     *
     * @param   PEAR_Error|int              $value
     * @return  string
     * @access  private
     */
    function _errorMessage($value)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR   => "An unknown error has occured.",
                SCIENCE_ASTRONOMY_ERROR_DATE_INVALID    => "The date you've provided is not a timestamp.",
                SCIENCE_ASTRONOMY_ERROR_RETFORM_INVALID => "The return format you've provided is not valid."
            );
        }

        if (Science_Astronomy::isError($value)) {
            $value = $value->getCode();
        }

        return isset($errorMessages[$value]) ? $errorMessages[$value] : $errorMessages[SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR];
    }
    // }}}

    // {{{ isError()
    /**
     * Checks for an error object, same as in PEAR
     *
     * @param   PEAR_Error|mixed            $value
     * @return  bool
     * @access  public
     */
    function isError($value)
    {
        return (is_object($value) && (strtolower(get_class($value)) == "pear_error" || is_subclass_of($value, "pear_error")));
    }
    // }}}

    // {{{ &raiseError()
    /**
     * Creates error, same as in PEAR with a customized flavor
     *
     * @param   int                         $code
     * @param   string                      $file
     * @param   int                         $line
     * @return  PEAR_Error
     * @access  private
     */
    function &raiseError($code = SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR, $file = "", $line = 0)
    {
        // This should improve the performance of the script, as PEAR is only included, when
        // really needed.
        include_once "PEAR.php";

        $message = "Science_Astronomy";
        if ($file != "" && $line > 0) {
            $message .= " (".basename($file).":".$line.")";
        }
        $message .= ": ".Science_Astronomy::_errorMessage($code);

        $error = PEAR::raiseError($message, $code, PEAR_ERROR_RETURN, E_USER_NOTICE, "Science_Astronomy_Error", null, false);
        return $error;
    }
    // }}}

    // {{{ calculateSunRiseSet()
    /**
     * Calculates sunrise and sunset for a location on a given date, accurate
     * to the minute within several centuries of the present. It correctly
     * describes what happens in the Arctic and Antarctic regions, where the
     * sun may not rise or set on a given date.
     *
     * The predicted times are given in local time, standard or daylight
     * saving, depending on what you enter as GMT offset. The azimuth of
     * sunrise and sunset is given, measured in degrees from true North. You
     * must determine your correct latitude and longitude to obtain valid
     * predictions for your location.
     *
     * This algorithm and the descriptions above were transferred from
     * the website of Stephen R. Schmitt, who in turn has implemented the
     * adapted code from a BASIC program in Sky & Telescope magazine,
     * August 1994, page 84. Also referred are the books "Astronomical
     * Algorithms" from Meeus and "Practical Astronomy with your Calculator".
     *
     * The date has to be entered as a timestamp!
     *
     * @param   int                         $date
     * @param   int                         $retformat
     * @param   float                       $latitude
     * @param   float                       $longitude
     * @param   float                       $gmt_offset
     * @return  PEAR_Error|array
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_DATE_INVALID
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_RETFORM_INVALID
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR
     * @access  public
     * @link    http://mysite.verizon.net/res148h4j/javascript/script_sun_rise_set.html
     */
    function calculateSunRiseSet($date, $retformat = null, $latitude = null, $longitude = null,  $gmt_offset = null)
    {
        // Date must be timestamp for now
        if (!is_int($date)) {
            return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_DATE_INVALID, __FILE__, __LINE__);
        }

        // Check for proper return format
        if ($retformat === null) {
            $retformat  = SCIENCE_ASTRONOMY_RET_STRING;
        } elseif (!in_array($retformat, array(SCIENCE_ASTRONOMY_RET_TIMESTAMP, SCIENCE_ASTRONOMY_RET_STRING, SCIENCE_ASTRONOMY_RET_DOUBLE)) ) {
            return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_RETFORM_INVALID, __FILE__, __LINE__);
        }

        // Set default values for coordinates
        if ($latitude === null) {
            $latitude   = SCIENCE_ASTRONOMY_DEFAULT_LATITUDE;
        } else {
            $latitude   = (float) $latitude;
        }
        if ($longitude === null) {
            $longitude  = SCIENCE_ASTRONOMY_DEFAULT_LONGITUDE;
        } else {
            $longitude  = (float) $longitude;
        }

        // Default value for GMT offset
        if ($gmt_offset === null) {
            $gmt_offset = date("Z", $date) / 3600;
        } else {
            $gmt_offset = (float) $gmt_offset;
        }

        $RAn = array(0.0, 0.0, 0.0);
        $Dec = array(0.0, 0.0, 0.0);
        $VHz = array(0.0, 0.0, 0.0);
        $ha  = array(0.0, 0.0, 0.0);

        // Calculate Julian Daycount to the second
        $JD = Science_Astronomy::dateToJD(date("Y", $date), date("n", $date), date("j", $date));

        // Calculate local sidereal time
        $t0 = Science_Astronomy::meanSiderealTime($JD, $gmt_offset, $longitude);
        $t0 = deg2rad($t0);

        // Julian day relative to Jan 1.5, 2000
        $JD = $JD - 2451545;

        $longitude  /= 360;
        $gmt_offset /= -24;
        $centuries   = $JD / 36525 + 1;

        // Get sun position at start of day
        $JD += $gmt_offset;
        $posSun = Science_Astronomy::_positionSun($JD, $centuries);
        $ra0  = $posSun["asc"];
        $dec0 = $posSun["dec"];

        // Get sun position at end of day
        $JD += 1;
        $posSun = Science_Astronomy::_positionSun($JD, $centuries);
        $ra1  = $posSun["asc"];
        $dec1 = $posSun["dec"];

        if ($ra1 < $ra0) $ra1 += 2 * M_PI;

        $RAn[0] = $ra0;
        $Dec[0] = $dec0;

        $foundRise = false;
        $foundSet  = false;
        $rise      = array("time" => null, "azi" => null);
        $set       = array("time" => null, "azi" => null);

        // Test an hour for an event
        for ($k = 0; $k < 24; $k++) {
            $ph = ($k + 1) / 24;

            $RAn[2] = $ra0  + ($k + 1) * ($ra1  - $ra0 ) / 24;
            $Dec[2] = $dec0 + ($k + 1) * ($dec1 - $dec0) / 24;

            $ha[0] = $t0 - $RAn[0] + $k * K1;
            $ha[2] = $t0 - $RAn[2] + $k * K1 + K1;

            // Hour angle at half hour
            $ha[1]  = ( $ha[2] +  $ha[0]) / 2;
            // Declination at half hour
            $Dec[1] = ($Dec[2] + $Dec[0]) / 2;

            $s = sin(DR * $latitude);
            $c = cos(DR * $latitude);

            // Refraction + sun semidiameter at horizon + parallax correction
            $z = cos(DR * 90.833);

            if ($k <= 0) {
                // First call of function
                $VHz[0] = $s * sin($Dec[0]) + $c * cos($Dec[0]) * cos($ha[0]) - $z;
            }

            $VHz[2] = $s * sin($Dec[2]) + $c * cos($Dec[2]) * cos($ha[2]) - $z;

            if (Science_Astronomy::_sgn($VHz[0]) == Science_Astronomy::_sgn($VHz[2])) {
                // Advance to next hour
                $RAn[0] = $RAn[2];
                $Dec[0] = $Dec[2];
                $VHz[0] = $VHz[2];
                // No event this hour
                continue;
            }

            $VHz[1] = $s * sin($Dec[1]) + $c * cos($Dec[1]) * cos($ha[1]) - $z;

            $a =  2 * $VHz[0] - 4      * $VHz[1] + 2 * $VHz[2];
            $b = -3 * $VHz[0] + 4      * $VHz[1] -     $VHz[2];
            $d = $b * $b      - 4 * $a * $VHz[0];

            if ($d < 0) {
                // Advance to next hour
                $RAn[0] = $RAn[2];
                $Dec[0] = $Dec[2];
                $VHz[0] = $VHz[2];
                // No event this hour
                continue;
            }

            $d = sqrt($d);
            $e = (-$b + $d) / (2 * $a);

            if (($e > 1) || ($e < 0)) {
                $e = (-$b - $d) / (2 * $a);
            }

            // Time of an event + round up
            $time = $k + $e + 1 / 120;

            // Azimuth of the moon at the event
            $hz = $ha[0] + $e * ($ha[2] - $ha[0]);
            $nz = -cos($Dec[1]) * sin($hz);
            $dz = $c * sin($Dec[1]) - $s * cos($Dec[1]) * cos($hz);
            $az = atan2($nz, $dz) / DR;

            if ($az < 0) $az += 360;

            if (($VHz[0] < 0) && ($VHz[2] > 0)) {
                $foundRise    = true;
                $rise["time"] = $time;
                $rise["azi"]  = round($az, 4);
            }

            if (($VHz[0] > 0) && ($VHz[2] < 0)) {
                $foundSet     = true;
                $set["time"]  = $time;
                $set["azi"]   = round($az, 4);
            }

            // Advance to next hour
            $RAn[0] = $RAn[2];
            $Dec[0] = $Dec[2];
            $VHz[0] = $VHz[2];
        }

        // Check for no rise and/or no set
        if (!$foundRise && !$foundSet) {
            if ($VHz[2] < 0) {
                $message = "Sun down all day";
            } else {
                $message = "Sun up all day";
            }
        } elseif (!$foundRise && $foundSet) {
            $message = "No sunrise this date";
        } elseif ($foundRise && !$foundSet) {
            $message = "No sunset this date";
        } else {
            $message = "";
        }

        $sun = array(
            "rise"    => $rise,
            "set"     => $set,
            "message" => $message
        );

        switch ($retformat) {
            case SCIENCE_ASTRONOMY_RET_TIMESTAMP:
                $sun["rise"]["time"] = intval($date - $date % (24 * 3600) + 3600 * $sun["rise"]["time"]);
                $sun["set"]["time"]  = intval($date - $date % (24 * 3600) + 3600 * $sun["set"]["time"]);
                break;
            case SCIENCE_ASTRONOMY_RET_STRING:
                if ($sun["rise"]["time"] == null) {
                    $sun["rise"]["time"] = "--:--";
                } else {
                    $hr = floor($sun["rise"]["time"]);
                    $mn = round(60 * ($sun["rise"]["time"] - $hr), 0);
                    $sun["rise"]["time"] = sprintf("%02d:%02d", $hr, $mn);
                }
                if ($sun["set"]["time"] == null) {
                    $sun["set"]["time"] = "--:--";
                } else {
                    $hr = floor($sun["set"]["time"]);
                    $mn = round(60 * ($sun["set"]["time"] - $hr), 0);
                    $sun["set"]["time"] = sprintf("%02d:%02d", $hr, $mn);
                }
                break;
            case SCIENCE_ASTRONOMY_RET_DOUBLE:
                break;
            default:
                return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR, __FILE__, __LINE__);
        }

        return $sun;
    }
    // }}}

    // {{{ calculateMoonRiseSet()
    /**
     * Calculates moonrise and moonset for a location on a given date, accurate
     * to the minute within several centuries of the present. It correctly
     * describes what happens in the Arctic and Antarctic regions, where the
     * moon may not rise or set on a given date.
     *
     * The predicted times are given in local time, standard or daylight
     * saving, depending on what you enter as GMT offset. The azimuth of
     * moonrise and moonset is given, measured in degrees from true North. You
     * must determine your correct latitude and longitude to obtain valid
     * predictions for your location.
     *
     * This algorithm and the descriptions above were transferred from
     * the website of Stephen R. Schmitt, who in turn has implemented the
     * adapted code from a BASIC program in Sky & Telescope magazine,
     * August 1989, page 87. Also referred are the books "Astronomical
     * Algorithms" from Meeus and "Practical Astronomy with your Calculator".
     *
     * The date has to be entered as a timestamp!
     *
     * @param   int                         $date
     * @param   int                         $retformat
     * @param   float                       $latitude
     * @param   float                       $longitude
     * @param   float                       $gmt_offset
     * @return  PEAR_Error|array
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_DATE_INVALID
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_RETFORM_INVALID
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR
     * @access  public
     * @link    http://mysite.verizon.net/res148h4j/javascript/script_moon_rise_set.html
     */
    function calculateMoonRiseSet($date, $retformat = null, $latitude = null, $longitude = null,  $gmt_offset = null)
    {
        // Date must be timestamp for now
        if (!is_int($date)) {
            return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_DATE_INVALID, __FILE__, __LINE__);
        }

        // Check for proper return format
        if ($retformat === null) {
            $retformat  = SCIENCE_ASTRONOMY_RET_STRING;
        } elseif (!in_array($retformat, array(SCIENCE_ASTRONOMY_RET_TIMESTAMP, SCIENCE_ASTRONOMY_RET_STRING, SCIENCE_ASTRONOMY_RET_DOUBLE)) ) {
            return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_RETFORM_INVALID, __FILE__, __LINE__);
        }

        // Set default values for coordinates
        if ($latitude === null) {
            $latitude   = SCIENCE_ASTRONOMY_DEFAULT_LATITUDE;
        } else {
            $latitude   = (float) $latitude;
        }
        if ($longitude === null) {
            $longitude  = SCIENCE_ASTRONOMY_DEFAULT_LONGITUDE;
        } else {
            $longitude  = (float) $longitude;
        }

        // Default value for GMT offset
        if ($gmt_offset === null) {
            $gmt_offset = date("Z", $date) / 3600;
        } else {
            $gmt_offset = (float) $gmt_offset;
        }

        $RAn = array(0.0, 0.0, 0.0);
        $Dec = array(0.0, 0.0, 0.0);
        $VHz = array(0.0, 0.0, 0.0);
        $ha  = array(0.0, 0.0, 0.0);
        $mp  = array(array(0.0, 0.0, 0.0), array(0.0, 0.0, 0.0), array(0.0, 0.0, 0.0));

        // Calculate Julian Daycount to the second
        $JD = Science_Astronomy::dateToJD(date("Y", $date), date("n", $date), date("j", $date));

        // Calculate local sidereal time
        $t0 = Science_Astronomy::meanSiderealTime($JD, $gmt_offset, $longitude);
        $t0 = deg2rad($t0);

        // Julian day relative to Jan 1.5, 2000
        $JD = $JD - 2451545;

        $longitude  /= 360;
        $gmt_offset /= -24;

        $JD += $gmt_offset;

        for ($k = 0; $k < 3; $k++) {
            $posMoon = Science_Astronomy::_positionMoon($JD);
            $mp[$k][0] = $posMoon["asc"];
            $mp[$k][1] = $posMoon["dec"];
            $mp[$k][2] = $posMoon["plx"];

            $JD += 0.5;
        }

        if ($mp[1][0] <= $mp[0][0]) $mp[1][0] += 2 * M_PI;
        if ($mp[2][0] <= $mp[1][0]) $mp[2][0] += 2 * M_PI;

        $RAn[0] = $mp[0][0];
        $Dec[0] = $mp[0][1];

        $foundRise = false;
        $foundSet  = false;
        $rise      = array("time" => null, "azi" => null);
        $set       = array("time" => null, "azi" => null);

        // Test an hour for an event
        for ($k = 0; $k < 24; $k++) {
            $ph = ($k + 1) / 24;

            $RAn[2] = Science_Astronomy::_interpolate($mp[0][0], $mp[1][0], $mp[2][0], $ph);
            $Dec[2] = Science_Astronomy::_interpolate($mp[0][1], $mp[1][1], $mp[2][1], $ph);

            if ($RAn[2] < $RAn[0]) $RAn[2] += 2 * M_PI;

            $ha[0] = $t0 - $RAn[0] + $k * K1;
            $ha[2] = $t0 - $RAn[2] + $k * K1 + K1;

            // Hour angle at half hour
            $ha[1]  = ( $ha[2] +  $ha[0]) / 2;
            // Declination at half hour
            $Dec[1] = ($Dec[2] + $Dec[0]) / 2;

            $s = sin(DR * $latitude);
            $c = cos(DR * $latitude);

            // Refraction + sun semidiameter at horizon + parallax correction
            $z = cos(DR * (90.567 - 41.685 / $mp[1][2]));

            if ($k <= 0) {
                // First call of function
                $VHz[0] = $s * sin($Dec[0]) + $c * cos($Dec[0]) * cos($ha[0]) - $z;
            }

            $VHz[2] = $s * sin($Dec[2]) + $c * cos($Dec[2]) * cos($ha[2]) - $z;

            if (Science_Astronomy::_sgn($VHz[0]) == Science_Astronomy::_sgn($VHz[2])) {
                // Advance to next hour
                $RAn[0] = $RAn[2];
                $Dec[0] = $Dec[2];
                $VHz[0] = $VHz[2];
                // No event this hour
                continue;
            }

            $VHz[1] = $s * sin($Dec[1]) + $c * cos($Dec[1]) * cos($ha[1]) - $z;

            $a =  2 * $VHz[2] - 4      * $VHz[1] + 2 * $VHz[0];
            $b =  4 * $VHz[1] - 3      * $VHz[0] -     $VHz[2];
            $d = $b * $b      - 4 * $a * $VHz[0];

            if ($d < 0) {
                // Advance to next hour
                $RAn[0] = $RAn[2];
                $Dec[0] = $Dec[2];
                $VHz[0] = $VHz[2];
                // No event this hour
                continue;
            }

            $d = sqrt($d);
            $e = (-$b + $d) / (2 * $a);

            if (($e > 1) || ($e < 0)) {
                $e = (-$b - $d) / (2 * $a);
            }

            // Time of an event + round up
            $time = $k + $e + 1 / 120;

            // Azimuth of the moon at the event
            $hz = $ha[0] + $e * ($ha[2] - $ha[0]);
            $nz = -cos($Dec[1]) * sin($hz);
            $dz = $c * sin($Dec[1]) - $s * cos($Dec[1]) * cos($hz);
            $az = atan2($nz, $dz) / DR;

            if ($az < 0) $az += 360;

            if (($VHz[0] < 0) && ($VHz[2] > 0)) {
                $foundRise    = true;
                $rise["time"] = $time;
                $rise["azi"]  = round($az, 4);
            }

            if (($VHz[0] > 0) && ($VHz[2] < 0)) {
                $foundSet     = true;
                $set["time"]  = $time;
                $set["azi"]   = round($az, 4);
            }

            // Advance to next hour
            $RAn[0] = $RAn[2];
            $Dec[0] = $Dec[2];
            $VHz[0] = $VHz[2];
        }

        // Check for no rise and/or no set
        if (!$foundRise && !$foundSet) {
            if ($VHz[2] < 0) {
                $message = "Moon down all day";
            } else {
                $message = "Moon up all day";
            }
        } elseif (!$foundRise && $foundSet) {
            $message = "No moonrise this date";
        } elseif ($foundRise && !$foundSet) {
            $message = "No moonset  this date";
        } else {
            $message = "";
        }

        $moon = array(
            "rise"    => $rise,
            "set"     => $set,
            "message" => $message
        );

        switch ($retformat) {
            case SCIENCE_ASTRONOMY_RET_TIMESTAMP:
                $moon["rise"]["time"] = intval($date - $date % (24 * 3600) + 3600 * $moon["rise"]["time"]);
                $moon["set"]["time"]  = intval($date - $date % (24 * 3600) + 3600 * $moon["set"]["time"]);
                break;
            case SCIENCE_ASTRONOMY_RET_STRING:
                if ($moon["rise"]["time"] == null) {
                    $moon["rise"]["time"] = "--:--";
                } else {
                    $hr = floor($moon["rise"]["time"]);
                    $mn = round(60 * ($moon["rise"]["time"] - $hr), 0);
                    $moon["rise"]["time"] = sprintf("%02d:%02d", $hr, $mn);
                }
                if ($moon["set"]["time"] == null) {
                    $moon["set"]["time"] = "--:--";
                } else {
                    $hr = floor($moon["set"]["time"]);
                    $mn = round(60 * ($moon["set"]["time"] - $hr), 0);
                    $moon["set"]["time"] = sprintf("%02d:%02d", $hr, $mn);
                }
                break;
            case SCIENCE_ASTRONOMY_RET_DOUBLE:
                break;
            default:
                return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR, __FILE__, __LINE__);
        }

        return $moon;
    }
    // }}}

    // {{{ calculateMoonPhase()
    /**
     * Calculates the moon's phase (age), distance, and position along the
     * ecliptic on any date within several thousand years in the past or
     * future.
     *
     * This algorithm and the descriptions above were transferred from
     * the website of Stephen R. Schmitt, who in turn has implemented the
     * adapted code from a BASIC program in Sky & Telescope magazine,
     * August 1989, page 87. Also referred are the books "Astronomical
     * Algorithms" from Meeus and "Practical Astronomy with your Calculator".
     *
     * The date has to be entered as a timestamp!
     *
     * @param   int                         $date
     * @return  PEAR_Error|array
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_DATE_INVALID
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_UNKNOWN_ERROR
     * @access  public
     * @link    http://http://mysite.verizon.net/res148h4j/javascript/script_moon_phase.html
     */
    function calculateMoonPhase($date)
    {
        // Date must be timestamp for now
        if (!is_int($date)) {
            return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_DATE_INVALID, __FILE__, __LINE__);
        }

        $moon = array(
            "age"    => 0.0, // Moon's age in days from New Moon
            "distance"  => 0.0, // Moon's distance in Earth radii
            "latitude"  => 0.0, // Moon's ecliptic latitude in degrees
            "longitude" => 0.0, // Moon's ecliptic longitude in degrees
            "phase"     => "",  // Moon's phase
            "zodiac"    => ""   // Moon's zodiac
        );

        $JD = Science_Astronomy::dateToJD(date("Y", $date), date("n", $date), date("j", $date), date("G", $date), date("i", $date), date("s", $date));

        // Calculate moon's age in days
        $IP = ($JD - 2451550.1) / 29.530588853;
        if (($IP = $IP - floor($IP)) < 0) $IP++;
        $age = $IP * 29.530588853;

        switch ($age) {
            case ($age <  1.84566):
                $phase = "New";             break;
            case ($age <  5.53699):
                $phase = "Waxing Crescent"; break;
            case ($age <  9.22831):
                $phase = "First Quarter";   break;
            case ($age < 12.91963):
                $phase = "Waxing Gibbous";  break;
            case ($age < 16.61096):
                $phase = "Full";            break;
            case ($age < 20.30228):
                $phase = "Waning Gibbous";  break;
            case ($age < 23.99361):
                $phase = "Last Quarter";    break;
            case ($age < 27.68493):
                $phase = "Waning Crescent"; break;
            default:
                $phase = "New";
        }

        // Convert phase to radians
        $IP = $IP * 2 * pi();

        // Calculate moon's distance
        $DP = ($JD - 2451562.2) / 27.55454988;
        if (($DP = $DP - floor($DP)) < 0) $DP++;
        $DP = $DP * 2 * pi();
        $distance = 60.4 - 3.3 * cos($DP) - 0.6 * cos(2 * $IP - $DP) - 0.5 * cos(2 * $IP);

        // Calculate moon's ecliptic latitude
        $NP = ($JD - 2451565.2) / 27.212220817;
        if (($NP = $NP - floor($NP)) < 0) $NP++;
        $NP = $NP * 2 * pi();
        $latitude = 5.1 * sin($NP);

        // Calculate moon's ecliptic longitude
        $RP = ($JD - 2451555.8) / 27.321582241;
        if (($RP = $RP - floor($RP)) < 0) $RP++;
        $longitude = 360 * $RP + 6.3 * sin($DP) + 1.3 * sin(2 * $IP - $DP) + 0.7 * sin(2 * $IP);
        if ($longitude >= 360) $longitude -= 360;

        switch ($longitude) {
            case ($longitude <  33.18):
                $zodiac = "Pisces";      break;
            case ($longitude <  51.16):
                $zodiac = "Aries";       break;
            case ($longitude <  93.44):
                $zodiac = "Taurus";      break;
            case ($longitude < 119.48):
                $zodiac = "Gemini";      break;
            case ($longitude < 135.30):
                $zodiac = "Cancer";      break;
            case ($longitude < 173.34):
                $zodiac = "Leo";         break;
            case ($longitude < 224.17):
                $zodiac = "Virgo";       break;
            case ($longitude < 242.57):
                $zodiac = "Libra";       break;
            case ($longitude < 271.26):
                $zodiac = "Scorpio";     break;
            case ($longitude < 302.49):
                $zodiac = "Sagittarius"; break;
            case ($longitude < 311.72):
                $zodiac = "Capricorn";   break;
            case ($longitude < 348.58):
                $zodiac = "Aquarius";    break;
            default:
                $zodiac = "Pisces";
        }

        $moon["age"]       = round($age, 4);
        $moon["distance"]  = round($distance, 4);
        $moon["latitude"]  = round($latitude, 4);
        $moon["longitude"] = round($longitude, 4);
        $moon["zodiac"]    = $zodiac;
        $moon["phase"]     = $phase;

        return $moon;
    }
    // }}}

    // {{{ _positionSun()
    /**
     * Calculate the sun's position using fundamental arguments
     *
     * This algorithm and the descriptions above were transferred from
     * the website of Stephen R. Schmitt, who in turn has implemented it from
     * "Low-Precision Formulae for Planetary Positions" from van Flanderen
     * and Pulkkinen, 1979.
     *
     * @param   float                       $JD
     * @param   float                       $ct
     * @return  array
     * @access  private
     */
    function _positionSun($JD, $ct)
    {
        $sun = array();

        $lo  = 0.779072 + 0.00273790931 * $JD;
        $lo -= floor($lo);
        $lo *= 2 * M_PI;

        $g  = 0.993126  + 0.0027377785  * $JD;
        $g -= floor($g);
        $g *= 2 * M_PI;

        $v  = 0.39785       * sin($lo);
        $v -= 0.01          * sin($lo - $g);
        $v += 0.00333       * sin($lo + $g);
        $v -= 0.00021 * $ct * sin($lo);

        $u  = 1 - 0.03349 * cos($g);
        $u -=     0.00014 * cos(2 * $lo);
        $u +=     0.00008 * cos($lo);

        $w  = -0.0001 - 0.04129 * sin(2 * $lo);
        $w +=  0.03211          * sin($g);
        $w +=  0.00104          * sin(2 * $lo - $g);
        $w -=  0.00035          * sin(2 * $lo + $g);
        $w -=  0.00008 * $ct    * sin($g);

        $s = $w / sqrt($u - $v * $v);        // Compute sun's right ascension ...
        $sun["asc"] = $lo + atan($s / sqrt(1 - $s * $s));

        $s = $v / sqrt($u);                  // and declination
        $sun["dec"] =       atan($s / sqrt(1 - $s * $s));

        return $sun;
    }

    // {{{ _positionMoon()
    /**
     * Calculate the moon's position using fundamental arguments
     *
     * This algorithm and the descriptions above were transferred from
     * the website of Stephen R. Schmitt, who in turn has implemented it from
     * "Low-Precision Formulae for Planetary Positions" from van Flanderen
     * and Pulkkinen, 1979.
     *
     * @param   float                       $JD
     * @return  array
     * @access  private
     */
    function _positionMoon($JD)
    {
        $moon = array();

        $h = 0.606434 + 0.03660110129 * $JD;
        $m = 0.374897 + 0.03629164709 * $JD;
        $f = 0.259091 + 0.0367481952  * $JD;
        $d = 0.827362 + 0.03386319198 * $JD;
        $n = 0.347343 - 0.00014709391 * $JD;
        $g = 0.993126 + 0.0027377785  * $JD;

        $h -= floor($h);
        $m -= floor($m);
        $f -= floor($f);
        $d -= floor($d);
        $n -= floor($n);
        $g -= floor($g);

        $h *= 2 * M_PI;
        $m *= 2 * M_PI;
        $f *= 2 * M_PI;
        $d *= 2 * M_PI;
        $n *= 2 * M_PI;
        $g *= 2 * M_PI;

        $v  = 0.39558 * sin($f + $n);
        $v += 0.082   * sin($f);
        $v += 0.03257 * sin($m - $f - $n);
        $v += 0.01092 * sin($m + $f + $n);
        $v += 0.00666 * sin($m - $f);
        $v -= 0.00644 * sin($m + $f - 2 * $d + $n);
        $v -= 0.00331 * sin($f - 2 * $d + $n);
        $v -= 0.00304 * sin($f - 2 * $d);
        $v -= 0.0024  * sin($m - $f - 2 * $d - $n);
        $v += 0.00226 * sin($m + $f);
        $v -= 0.00108 * sin($m + $f - 2 * $d);
        $v -= 0.00079 * sin($f - $n);
        $v += 0.00078 * sin($f + 2 * $d + $n);

        $u  = 1 - 0.10828 * cos($m);
        $u -=     0.0188  * cos($m - 2 * $d);
        $u -=     0.01479 * cos(2 * $d);
        $u +=     0.00181 * cos(2 * $m - 2 * $d);
        $u -=     0.00147 * cos(2 * $m);
        $u -=     0.00105 * cos(2 * $d - $g);
        $u -=     0.00075 * cos($m - 2 * $d + $g);

        $w  = 0.10478 * sin($m);
        $w -= 0.04105 * sin(2 * $f + 2 * $n);
        $w -= 0.0213  * sin($m - 2 * $d);
        $w -= 0.01779 * sin(2 * $f + $n);
        $w += 0.01774 * sin($n);
        $w += 0.00987 * sin(2 * $d);
        $w -= 0.00338 * sin($m - 2 * $f - 2 * $n);
        $w -= 0.00309 * sin($g);
        $w -= 0.0019  * sin(2 * $f);
        $w -= 0.00144 * sin($m + $n);
        $w -= 0.00144 * sin($m - 2 * $f - $n);
        $w -= 0.00113 * sin($m + 2 * $f + 2 * $n);
        $w -= 0.00094 * sin($m - 2 * $d + $g);
        $w -= 0.00092 * sin(2 * $m - 2 * $d);

        $s = $w / sqrt($u - $v * $v);        // Compute moon's right ascension ...
        $moon["asc"] = $h + atan($s / sqrt(1 - $s * $s));

        $s = $v / sqrt($u);                  // declination ...
        $moon["dec"] =      atan($s / sqrt(1 - $s * $s));

        $moon["plx"] = 60.40974 * sqrt($u);  // and parallax

        return $moon;
    }


    // {{{ meanSiderealTime()
    /**
     * Calculate the Mean Sidereal Time. If you leave out the longitude and
     * the timezone, you'll get the Greenwich Mean Sidereal Time or GMST.
     *
     * This algorithm and the descriptions above were transferred from
     * the website of Stephen R. Schmitt, who in turn has implemented it from
     * "Astronomical Algorithms" from Meeus.
     *
     * @param   float                       $JD
     * @param   float                       $timezone
     * @param   float                       $longitude
     * @return  float
     * @access  public
     */
    function meanSiderealTime($JD, $timezone = 0, $longitude = 0)
    {
        $JD0 = $JD - 2451545.0 - $timezone/24;
        $JC  = $JD0 / 36525;
        $LMST = 280.46061837 + 360.98564736628603 * $JD0 + 0.000387933 * pow($JC, 2) - pow($JC, 3) / 38710000 + $longitude;
        $LMST = ($LMST/360 - floor($LMST/360)) * 360;

        return $LMST;
    }
    // }}}

    // {{{ dateToJD()
    /**
     * Calculate the Julian Day Number (JD) for a given date and time.
     *
     * This algorithm and the descriptions above were transferred from
     * the website of Stephen R. Schmitt, who in turn has implemented it from
     * "Astronomical Algorithms" from Meeus.
     *
     * @param   int                         $year
     * @param   int                         $month
     * @param   int                         $day
     * @param   int                         $hour
     * @param   int                         $minute
     * @param   int                         $second
     * @return  PEAR_Error|float
     * @throws  PEAR_Error::SCIENCE_ASTRONOMY_ERROR_DATE_INVALID
     * @access  public
     */
    function dateToJD($year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        // Calculate Julian Daycount to the second
        if ($month > 2) {
            $YY = $year;
            $MM = $month;
        } else {
            $YY = $year  - 1;
            $MM = $month + 12;
        }

        $DD = $day;
        $HH = $hour/24 + $minute/1440 + $second/86400;

        // Check for Gregorian date and adjust JD appropriately
        if (($year*10000 + $month*100 + $day) >= 15821015) {
            $A = floor($YY/100);
            $B = 2 - $A + floor($A/4);
        } elseif (($year*10000 + $month*100 + $day) <= 15821004) {
            $B = 0;
        } else {
            return Science_Astronomy::raiseError(SCIENCE_ASTRONOMY_ERROR_DATE_INVALID, __FILE__, __LINE__);
        }

        $JD = floor(365.25*($YY+4716)) + floor(30.6001*($MM+1)) + $DD + $HH + $B - 1524.5;

        return $JD;
    }
    // }}}

    // {{{ _interpolate()
    /**
     * Three-point interpolation
     *
     * @param   float                       $f0
     * @param   float                       $f1
     * @param   float                       $f2
     * @param   float                       $p
     * @return  float
     * @access  public
     */
    function _interpolate($f0, $f1, $f2, $p)
    {
        $a = $f1 - $f0;
        $b = $f2 - $f1 - $a;
        $f = $f0 + $p * (2 * $a + $b * (2 * $p - 1));

        return $f;
    }
    // }}}

    // {{{ _sgn()
    /**
     * Sign of a number
     *
     * @param   float                       $x
     * @return  int
     * @access  public
     */
    function _sgn($x)
    {
        if ($x > 0.0) {
            $rv =  1;
        } elseif ($x < 0.0) {
            $rv = -1;
        } else {
            $rv =  0;
        }

        return $rv;
    }
    // }}}
}
// }}}
?>
