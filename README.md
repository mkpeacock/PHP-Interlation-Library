# PHP Interlation Library

This library is used to "interlace" a number of arrays into a single array, with a unified "master key".  The unique set of "master keys" from all arrays is used as the keys for the resulting array.  If one of the input arrays is missing a value for a particular master key, it assumes the last known value.  

This has a particular use-case when you only log data when it changes, and you need to perform calculations against a range of data sets over a time period.  This was originally developed for Smith Electric Vehicles Corp as part, within their telematics project.  As the functionality is generic, and not specific to the project, permission was granted for this code to be open-sourced.

## Usage

- coming soon
