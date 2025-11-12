import { Component } from "vue";

export class EntityAction<T> {

    constructor(public action: string, public icon: Component, public actionType: string, public entityKey: string, public classStyle?: string) {
        this.action = action;
        this.icon = icon;
        this.actionType = actionType;
        this.entityKey = entityKey;
        this.classStyle = classStyle;
    }

    public click(context: T) {
        window.dispatchEvent(

            new CustomEvent(getActionEventName(this.actionType, this.entityKey), {
                detail: context,
            }),
        );
    }
}
export const getActionEventName = (actionId: string, entityKey: string) => {
    return `${actionId}-${entityKey}`
}