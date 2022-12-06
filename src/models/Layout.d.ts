import { Component } from 'vue/types/vue';
import { RecordPropsDefinition } from 'vue/types/options';

declare namespace Layout {
    export const id: string;
    export const icon: string;
    export const name: string;

    const _default: boolean | null;
    export { _default as default };

    export const render: Component;

    export const props: RecordPropsDefinition;
}

export default Layout;
