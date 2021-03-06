/**
 * Copyright (c) Enalean, 2020 - present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

import {
    appendGroupedOptionsToSourceSelectBox,
    appendSimpleOptionsToSourceSelectBox,
} from "../test-helpers/select-box-options-generator";
import { DropdownContentRenderer } from "./DropdownContentRenderer";
import { BaseComponentRenderer } from "../renderers/BaseComponentRenderer";

describe("dropdown-content-renderer", () => {
    let select: HTMLSelectElement, dropdown: Element, dropdown_list: Element;

    beforeEach(() => {
        select = document.createElement("select");

        const { dropdown_element, dropdown_list_element } = new BaseComponentRenderer(
            select
        ).renderBaseComponent();

        dropdown = dropdown_element;
        dropdown_list = dropdown_list_element;
    });

    it("renders grouped list items", () => {
        appendGroupedOptionsToSourceSelectBox(select);

        new DropdownContentRenderer(select, dropdown_list).renderListPickerDropdownContent();

        expect(dropdown.innerHTML).toMatchSnapshot();
    });

    it("renders simple list items", () => {
        appendSimpleOptionsToSourceSelectBox(select);
        new DropdownContentRenderer(select, dropdown_list).renderListPickerDropdownContent();

        expect(dropdown.innerHTML).toMatchSnapshot();
    });

    it("when the source option is disabled, then the list item should be disabled", () => {
        const disabled_option = document.createElement("option");
        disabled_option.setAttribute("disabled", "disabled");
        disabled_option.setAttribute("value", "You can't select me");

        select.appendChild(disabled_option);

        new DropdownContentRenderer(select, dropdown_list).renderListPickerDropdownContent();

        const disabled_list_item = dropdown.querySelector(
            ".list-picker-dropdown-option-value-disabled"
        );

        expect(disabled_list_item).not.toBeNull();
    });
});
