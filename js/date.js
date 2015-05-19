/* This file is part of:
 *    Jalali, a Gregorian to Jalali and inverse date convertor
 * Copyright (C) 2001  Roozbeh Pournader <roozbeh@sharif.edu>
 * Copyright (C) 2001  Mohammad Toossi <mohammad@bamdad.org>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You can receive a copy of GNU Lesser General Public License at the
 * World Wide Web address <http://www.gnu.org/licenses/lgpl.html>.
 *
 * For licensing issues, contact The FarsiWeb Project Group,
 * Computing Center, Sharif University of Technology,
 * PO Box 11365-8515, Tehran, Iran, or contact us the
 * email address <FWPG@sharif.edu>.
 */

/* Changes:
 * 
 * 2001-Sep-21:
 *	Fixed a bug with "30 Esfand" dates, reported by Mahmoud Ghandi
 *
 * 2001-Sep-20:
 *	First LGPL release, with both sides of conversions
 */
 
//#include <stdio.h>
//#include <stdlib.h>
//#include <time.h>
 
//function gregorian_to_jalali
function GTJ(
	g_y,
	g_m,
	g_d,
	choice
	)

{
var g_days_in_month = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var j_days_in_month = new Array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
var j_month_name = new Array("", "Farvardin", "Ordibehesht", "Khordad", "Tir",
                          "Mordad", "Shahrivar", "Mehr", "Aban", "Azar",
                          "Dey", "Bahman", "Esfand");

   var gy, gm, gd;
   var jy, jm, jd;
   var g_day_no, j_day_no;
   var j_np;
 
   var i;
   gy = g_y-1600;
   gm = g_m-1;
   gd = g_d-1;
 
   g_day_no = 365*gy+Math.floor((gy+3)/4)-Math.floor((gy+99)/100)+Math.floor((gy+399)/400);
   for (i=0;i<gm;++i)
      g_day_no += g_days_in_month[i];
   if (gm>1 && ((gy%4==0 && gy%100!=0) || (gy%400==0)))
      /* leap and after Feb */
      ++g_day_no;
   g_day_no += gd;
 
   j_day_no = g_day_no-79;
 
   j_np = Math.floor(j_day_no / 12053);
   j_day_no %= 12053;
 
   jy = 979+33*j_np+4*Math.floor((j_day_no/1461));
   j_day_no %= 1461;
 
   if (j_day_no >= 366) {
      jy += Math.floor((j_day_no-1)/365);
      j_day_no = (j_day_no-1)%365;
   }
 
   for (i = 0; i < 11 && j_day_no >= j_days_in_month[i]; ++i) {
      j_day_no -= j_days_in_month[i];
   }
   jm = i+1;
   jd = j_day_no+1;
   
   var strjm = new String(jm);
   var strjd = new String(jd);
   
   if (jm<10) 
		strjm = "0"+jm;
   if (jd<10) 
		strjd = "0"+jd;
   
   if (choice == 'y' || choice =='Y')
		return String(jy);
   else if (choice == 'm' || choice =='M')
		return strjm;
   else if (choice == 'd' || choice =='D')
		return strjd;
   else
		return String(jy)+'/'+strjm+'/'+strjd;
   
}
//------------------------------------------------------------------------
//void jalali_to_gregorian(
function JTG(
	j_y,
	j_m,
	j_d,
	choice)
{
var g_days_in_month = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var j_days_in_month = new Array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
var j_month_name = new Array("", "Farvardin", "Ordibehesht", "Khordad", "Tir",
                          "Mordad", "Shahrivar", "Mehr", "Aban", "Azar",
                          "Dey", "Bahman", "Esfand");

   var gy, gm, gd;
   var jy, jm, jd;
   var g_day_no, j_day_no;
   var leap;

   var i;

   jy = j_y-979;
   jm = j_m-1;
   jd = j_d-1;

   j_day_no = 365*jy + Math.floor(jy/33)*8 + Math.floor((jy%33+3)/4);
   for (i=0; i < jm; ++i)
      j_day_no += j_days_in_month[i];

   j_day_no += jd;

   g_day_no = j_day_no+79;

   gy = 1600 + 400*Math.floor((g_day_no)/(146097)); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
   g_day_no = g_day_no % 146097;

   leap = 1;
   if (g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
   {
      g_day_no--;
      gy += 100*Math.floor((g_day_no)/(36524)); /* 36524 = 365*100 + 100/4 - 100/100 */
      g_day_no = g_day_no % 36524;
      
      if (g_day_no >= 365)
         g_day_no++;
      else
         leap = 0;
   }

   gy += 4*Math.floor((g_day_no)/(1461)); /* 1461 = 365*4 + 4/4 */
   g_day_no %= 1461;

   if (g_day_no >= 366) {
      leap = 0;

      g_day_no--;
      gy += Math.floor((g_day_no)/(365));
      g_day_no = g_day_no % 365;
   }

   for (i = 0; g_day_no >= g_days_in_month[i] + (i == 1 && leap); i++)
      g_day_no -= g_days_in_month[i] + (i == 1 && leap);
   gm = i+1;
   gd = g_day_no+1;

   var strgm = new String(gm);
   var strgd = new String(gd);
   
   if (gm<10) 
		strgm = "0"+gm;
   if (gd<10) 
		strgd = "0"+gd;
   
   if (choice == 'y' || choice =='Y')
		return String(gy);
   else if (choice == 'm' || choice =='M')
		return strgm;
   else if (choice == 'd' || choice =='D')
		return strgd;
   else
		return String(gy)+'/'+strgm+'/'+strgd;

}
//------------------------------------------------------------------------
function jDate()
{
var j = new String();
today = new Date();
return GTJ(today.getFullYear(), today.getMonth()+1,today.getDate(),'a');
}

//------------------------------------------------------------------------
function localNumbers(mynum)
{
var k;
var digits;
var partone,parttwo;
var temp;
temp = "";
FNums = new Array("&#1632;","&#1633;","&#1634;","&#1635;","&#1636;","&#1637;","&#1638;","&#1639;","&#1640;","&#1641;");
k = 0;
digits = Math.floor(Math.log(mynum)/Math.LN10); 
partone = mynum;
for (ii=digits;ii>=0;--ii) {
	partone = partone - k * Math.pow(10,ii+1);
	parttwo = partone / Math.pow(10,ii);
	k = Math.floor(parttwo);
	temp += FNums[k];
}
return temp;
}
//----------------------------------------------------------------------
function jalaliDate()
{
var j;
d = new Array("&#1610;&#1705;&#1588;&#1606;&#1576;&#1607;","&#1583;&#1608;&#1588;&#1606;&#1576;&#1607;","&#1587;&#1607; &#1588;&#1606;&#1576;&#1607;","&#1670;&#1607;&#1575;&#1585;&#1588;&#1606;&#1576;&#1607;","&#1662;&#1606;&#1580;&#1588;&#1606;&#1576;&#1607;","&#1580;&#1605;&#1593;&#1607;","&#1588;&#1606;&#1576;&#1607;"); 
today = new Date(); 
CMA = new Array('&#1601;&#1585;&#1608;&#1585;&#1583;&#1610;&#1606;','&#1575;&#1585;&#1583;&#1610;&#1576;&#1607;&#1588;&#1578;','&#1582;&#1585;&#1583;&#1575;&#1583;','&#1578;&#1610;&#1585;','&#1605;&#1585;&#1583;&#1575;&#1583;','&#1588;&#1607;&#1585;&#1610;&#1608;&#1585;','&#1605;&#1607;&#1585;','&#1570;&#1576;&#1575;&#1606;','&#1570;&#1584;&#1585;','&#1583;&#1610;','&#1576;&#1607;&#1605;&#1606;','&#1575;&#1587;&#1601;&#1606;&#1583;'); 

j = d[today.getDay()] + ' '; 

myday = GTJ(today.getFullYear(), today.getMonth()+1,today.getDate(),'d');

j += localNumbers(myday);

j+= ' ';

j+= CMA[GTJ(today.getFullYear(),today.getMonth()+1,today.getDate(),'m')-1]+' ';

myyear = GTJ(today.getFullYear(), today.getMonth()+1,today.getDate(),'y');

j += localNumbers(myyear);

return j;
}
//-------------------------------------------------------------------------
