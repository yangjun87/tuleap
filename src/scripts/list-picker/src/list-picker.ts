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

import { ListPickerOptions } from "./type";
import { DropdownContentRenderer } from "./helpers/DropdownContentRenderer";
import { SelectionManager } from "./helpers/SelectionManager";
import { EventManager } from "./helpers/EventManager";
import { DropdownToggler } from "./helpers/DropdownToggler";
import { BaseComponentRenderer } from "./renderers/BaseComponentRenderer";

export function createListPicker(
    source_select_box: HTMLSelectElement,
    options?: ListPickerOptions
): void {
    const base_renderer = new BaseComponentRenderer(source_select_box, options);
    const {
        wrapper_element,
        list_picker_element,
        dropdown_element,
        selection_element,
        placeholder_element,
        dropdown_list_element,
    } = base_renderer.renderBaseComponent();

    const dropdown_toggler = new DropdownToggler(
        list_picker_element,
        dropdown_element,
        dropdown_list_element
    );
    const selection_manager = new SelectionManager(
        source_select_box,
        dropdown_element,
        selection_element,
        placeholder_element,
        dropdown_toggler
    );

    const dropdown_content_renderer = new DropdownContentRenderer(
        source_select_box,
        dropdown_list_element
    );

    dropdown_content_renderer.renderListPickerDropdownContent();

    const event_manager = new EventManager(
        document,
        wrapper_element,
        dropdown_element,
        source_select_box,
        selection_manager,
        dropdown_toggler
    );

    event_manager.attachEvents();
    selection_manager.initSelection(placeholder_element);
}
