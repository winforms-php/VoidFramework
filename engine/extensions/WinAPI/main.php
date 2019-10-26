<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     WinAPI control unit
 * @copyright   2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Podvirnyy Nikita:
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

namespace VoidEngine;

class WinAPI
{
    protected $WinAPI;

    public function __construct ()
    {
        /**
         * Большинство функций было взято из класса "WinAPI" проекта "DevelStudio XL"
         * @author Дима Скрипов
         * 
         * @see <https://vk.com/evsoft>
         * @see <https://vk.com/magic.breeze>
         */
        $this->WinAPI = \FFI::cdef ('
            struct LPCTSTR
            {
                char string;
            }; 

            int SendMessageA (int hWnd, int Msg, int wParam, char* lParam);
            int SendMessageW (int hWnd, int Msg, int wParam, char* lParam);
            int FindWindowA (char* lpClassName, char* lpWindowName);
            int FindWindowExA (int hwndParent, int hwndChildAfter, struct LPCTSTR *lpszClass, struct LPCTSTR *lpszWindow);
            int SetWindowPos (int hWnd, int hWndInsertAfter, int X, int Y, int W, int H, int uFlags);
            int CreateWindowExA (char* dwExStyle, char* lpClassName, char* lpWindowName, char* dwStyle, int X, int Y, int nWidth, int nHeight, int hWndParent, int hMenu, int hInstance, int lpParam);
            int DestroyWindow (int hWnd);
            int GetTopWindow (int hWnd);
            bool SetForegroundWindow (int hWnd);
            int IsWindowEnabled (int hWnd);
            int IsWindow (int hWnd);
            int IsWindowVisible (int hWnd);
            int OpenIcon (int hWnd);
            int GetActiveWindow ();
            int EndTask (int hWnd, int fShutDown, int fForce);
            int GetAsyncKeyState (int vKey);
            int GetForegroundWindow ();
            int GetDesktopWindow ();
            int GetShellWindow ();
            int GetWindowLongA (int hWnd , int nIndex);
            int GetFocus ();
            int CloseWindow (int hWnd);
            int GetLastActivePopup (int hWnd);
            int EnableWindow (int hWnd, bool bEnable);
            int ActivateKeyboardLayout (int hkl, int Flags);
            int GetWindowThreadProcessId (int hWnd, int lpdwProcessId);
			int GetKeyboardLayout (int idThread);
        ', 'User32.dll');
    }

    public function __call ($method, $args)
    {
        return method_exists ($this, $method) ?
            $this->$method (...$args) :
            $this->WinAPI->$method (...$args);
    }
}
