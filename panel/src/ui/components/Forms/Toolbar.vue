<template>
  <nav v-if="buttons" class="k-toolbar" @click.stop>

    <template v-for="(button, buttonIndex) in layout">

      <!-- divider -->
      <template v-if="button.divider">
        <span :key="buttonIndex" class="k-toolbar-divider" />
      </template>

      <!-- dropdown -->
      <template v-else-if="button.dropdown">
        <k-dropdown :key="buttonIndex">
          <k-button
            :icon="button.icon"
            :key="buttonIndex"
            tabindex="-1"
            class="k-toolbar-button"
            @click="$refs[buttonIndex + '-dropdown'][0].toggle()"
          />
          <k-dropdown-content :ref="buttonIndex + '-dropdown'">
            <k-dropdown-item
              v-for="(dropdownItem, dropdownItemIndex) in button.dropdown"
              :key="dropdownItemIndex"
              :icon="dropdownItem.icon"
              @click="command(dropdownItem.command, dropdownItem.args)"
            >
              {{ dropdownItem.label }}
            </k-dropdown-item>
          </k-dropdown-content>
        </k-dropdown>
      </template>

      <!-- single button -->
      <template v-else>
        <k-button
          :icon="button.icon"
          :key="buttonIndex"
          tabindex="-1"
          class="k-toolbar-button"
          @click="command(button.command, button.args)"
        />
      </template>

    </template>

  </nav>
</template>

<script>

const list = function(type) {

  this.command("insert", (input, selection) => {

    let html = [];

    selection.split("\n").forEach((line, index) => {
      let prepend = type === "ol" ? index + 1 + "." : "-";
      html.push(prepend + " " + line);
    });

    return html.join("\n");
  });

};

export default {
  commands: {
    headlines: {
      label: "Headline",
      icon: "title",
      dropdown: {
        h1: {
          label: "Headline 1",
          icon: "title",
          command: "prepend",
          args: "#",
        },
        h2: {
          label: "Headline 2",
          icon: "title",
          command: "prepend",
          args: "##"
        },
        h3: {
          label: "Headline 3",
          icon: "title",
          command: "prepend",
          args: "###"
        }
      }
    },
    bold: {
      label: "Bold",
      icon: "bold",
      command: "wrap",
      args: "**",
      shortcut: "b"
    },
    italic: {
      label: "Italic",
      icon: "italic",
      command: "wrap",
      args: "*",
      shortcut: "i"
    },
    link: {
      label: "Link",
      icon: "url",
      shortcut: "l",
      command: "dialog",
      args: "link"
    },
    email: {
      label: "Email",
      icon: "email",
      shortcut: "e",
      command: "dialog",
      args: "email"
    },
    code: {
      label: "Code",
      icon: "code",
      command: "wrap",
      args: "`",
    },
    ul: {
      label: "Bullet List",
      icon: "list-bullet",
      command() {
        return list.apply(this, ["ul"]);
      },
    },
    ol: {
      label: "Ordered List",
      icon: "list-numbers",
      command() {
        return list.apply(this, ["ol"]);
      },
    }
  },
  layout: [
    "headlines",
    "bold",
    "italic",
    "|",
    "link",
    "email",
    "code",
    "|",
    "ul",
    "ol"
  ],
  props: {
    buttons: {
      type: [Boolean, Array],
      default: true,
    }
  },
  data() {

    let layout    = {};
    let shortcuts = {};
    let buttons   = [];

    if (this.buttons === false) {
      return layout;
    }

    if (Array.isArray(this.buttons)) {
      buttons = this.buttons;
    }

    if (Array.isArray(this.buttons) !== true) {
      buttons = this.$options.layout;
    }

    buttons.forEach((item, index) => {
      if (item === "|") {
        layout["divider-" + index] = { divider: true };
      } else if (this.$options.commands[item]) {
        let button = this.$options.commands[item];
        layout[item] = button;

        if (button.shortcut) {
          shortcuts[button.shortcut] = item;
        }
      }
    });

    return {
      layout: layout,
      shortcuts: shortcuts
    };
  },
  mounted() {
    this.$events.$on('click', this.blur);
    this.$events.$on('keydown.esc', this.cancel);
  },
  destroyed() {
    this.$events.$off('click', this.blur);
    this.$events.$off('keydown.esc', this.cancel);
  },
  methods: {
    blur() {
      this.$emit('blur');
    },
    cancel() {
      this.$emit('cancel');
    },
    command(command, callback) {
      if (typeof command === "function") {
        command.apply(this);
      } else {
        this.$emit("command", command, callback);
      }
    },
    shortcut(shortcut, $event) {

      if (this.shortcuts[shortcut]) {

        const button = this.layout[this.shortcuts[shortcut]];

        if (!button) {
          return false;
        }

        $event.preventDefault();

        this.command(button.command, button.args);

      }
    }
  }
}
</script>

<style lang="scss">
.k-toolbar {
  display: flex;
  background: $color-white;
  box-shadow: $box-shadow;
  border: 1px solid $color-border;
  border-radius: $border-radius;
}
.k-toolbar-divider {
  width: 1px;
  background: $color-border;
}
.k-toolbar-button {
  padding: 0 .75rem;
  height: 36px;
}
.k-toolbar-button:hover {
  background: rgba($color-background, .5);
}
</style>
