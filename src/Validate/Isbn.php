<?php
/**
 * This file is part of OPUS. The software OPUS has been originally developed
 * at the University of Stuttgart with funding from the German Research Net,
 * the Federal Department of Higher Education and Research and the Ministry
 * of Science, Research and the Arts of the State of Baden-Wuerttemberg.
 *
 * OPUS 4 is a complete rewrite of the original OPUS software and was developed
 * by the Stuttgart University Library, the Library Service Center
 * Baden-Wuerttemberg, the Cooperative Library Network Berlin-Brandenburg,
 * the Saarland University and State Library, the Saxon State Library -
 * Dresden State and University Library, the Bielefeld University Library and
 * the University Library of Hamburg University of Technology with funding from
 * the German Research Foundation and the European Regional Development Fund.
 *
 * LICENCE
 * OPUS is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or any later version.
 * OPUS is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License
 * along with OPUS; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * @category    Framework
 * @package     Opus_Validate
 * @author      Ralf Claussnitzer <ralf.claussnitzer@slub-dresden.de>
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2008-2018, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

namespace Opus\Validate;

/**
 * Validator for Isbn field.
 *
 * @category    Framework
 * @package     Opus_Validate
 */
class Isbn extends \Zend_Validate_Abstract
{

    /**
     * Error message key for invalid check digit.
     *
     */
    const MSG_CHECK_DIGIT = 'checkdigit';


    /**
     * Error message key for malformed ISBN.
     *
     */
    const MSG_FORM = 'form';

    /**
     * Error message templates.
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::MSG_CHECK_DIGIT => "The check digit of '%value%' is not valid.",
        self::MSG_FORM => "'%value%' is malformed."
    ];

    /**
     * Validate the given ISBN string using ISBN-10 or ISBN-13 validators respectivly.
     *
     * @param string $value An ISBN number.
     * @return boolean True if the given ISBN string is valid.
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        switch (count($this->extractDigits($value))) {
            case 10:
                $isbn_validator = new Isbn10();
                break;
            case 13:
                $isbn_validator = new Isbn13();
                break;
            default:
                $this->_error(self::MSG_FORM);
                $result = false;
                break;
        }

        if (!isset($isbn_validator) === false) {
            $result = $isbn_validator->isValid($value);
            foreach ($isbn_validator->getErrors() as $error) {
                $this->_error($error);
            }
        }

        return $result;
    }

    public function extractDigits($value)
    {
        return str_split(preg_replace('(-|\s)', '', $value));
    }
}
