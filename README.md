Those are the WBZ Archive website files.

The top level requires the folder "check/u8", "szsdecomp/XX" and "thumbnail/XX" with XX being folder from 00 - 99. XX also is the last two digits of the 5 digit track ID.

Here are scripts I used locally before sending files to the server:

wszst copy --wbz --norm W:\Mario_Kart_Wii\import\26549.szs -d W:\Mario_Kart_Wii\wbz\49\%n.wbz -o
wszst copy --wbz --norm W:\Mario_Kart_Wii\import\26550.szs -d W:\Mario_Kart_Wii\wbz\50\%n.wbz -o

wszst copy --szs W:\Mario_Kart_Wii\wbz\49\26549.wbz -d W:\Mario_Kart_Wii\szsdecomp\49\%n.szs -o
wszst copy --szs W:\Mario_Kart_Wii\wbz\50\26550.wbz -d W:\Mario_Kart_Wii\szsdecomp\50\%n.szs -o

wszst decompress W:\Mario_Kart_Wii\szsdecomp\49\26549.szs
wszst decompress W:\Mario_Kart_Wii\szsdecomp\50\26550.szs

move W:\Mario_Kart_Wii\szsdecomp\49\26549.szs W:\Mario_Kart_Wii\szs\49\26549.szs
move W:\Mario_Kart_Wii\szsdecomp\50\26550.szs W:\Mario_Kart_Wii\szs\50\26550.szs

copy W:\Mario_Kart_Wii\szs\49\26549.szs W:\Mario_Kart_Wii\dolphinsdcard\mkw-sp\thumbnails\inputs\YY\26549.szs                || with YY being the track slot it should be ran on
copy W:\Mario_Kart_Wii\szs\50\26550.szs W:\Mario_Kart_Wii\dolphinsdcard\mkw-sp\thumbnails\inputs\YY\26550.szs                || with YY being the track slot it should be ran on

python xfb2png.py W:\Mario_Kart_Wii\outputs\YY\26549.szs.xfb W:\Mario_Kart_Wii\thumbnail\49\26549
python xfb2png.py W:\Mario_Kart_Wii\outputs\YY\26550.szs.xfb W:\Mario_Kart_Wii\thumbnail\50\26550

python xfb2png169.py --unstretch  W:\Mario_Kart_Wii\169outputs\12\26549.szs.xfb W:\Mario_Kart_Wii\preview\49\26549
python xfb2png169.py --unstretch  W:\Mario_Kart_Wii\169outputs\12\26550.szs.xfb W:\Mario_Kart_Wii\preview\50\26550

After those scripts the wbz files and the thumbnails were synchronized with the server.

On the server the following scripts were then ran:

services/convertjpg.php
services/slots.php
services/sha1sha3.php
services/trackcheck2.php
services/trackfilesize.php
services/trackstgi.php
