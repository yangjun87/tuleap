/*
 * Copyright (c) Enalean, 2019 - present. All Rights Reserved.
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

import { shallowMount } from "@vue/test-utils";
import ParentCell from "./ParentCell.vue";
import ParentCard from "../Card/ParentCard.vue";
import NoMappingMessage from "./NoMappingMessage.vue";

describe("ParentCell", () => {
    it("displays the parent card in its own cell", () => {
        const wrapper = shallowMount(ParentCell, {
            propsData: {
                card: {
                    id: 43,
                    has_children: true
                }
            }
        });

        expect(wrapper.contains(ParentCard)).toBe(true);
        expect(wrapper.contains(NoMappingMessage)).toBe(false);
    });

    it("displays a no mapping message if card does not have any children", () => {
        const wrapper = shallowMount(ParentCell, {
            propsData: {
                card: {
                    id: 43,
                    has_children: false
                }
            }
        });

        expect(wrapper.contains(ParentCard)).toBe(true);
        expect(wrapper.contains(NoMappingMessage)).toBe(true);
    });
});