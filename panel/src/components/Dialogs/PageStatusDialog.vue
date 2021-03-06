<template>
  <k-dialog
    ref="dialog"
    :button="$t('change')"
    size="medium"
    theme="positive"
    @submit="submit"
  >
    <k-form
      ref="form"
      :fields="fields"
      v-model="form"
      @submit="changeStatus"
    />
  </k-dialog>
</template>

<script>
import DialogMixin from "@/mixins/dialog.js";

export default {
  mixins: [DialogMixin],
  data() {
    return {
      page: {
        id: null
      },
      isBlocked: false,
      isIncomplete: false,
      form: {
        status: null,
        position: null
      },
      states: {}
    };
  },
  computed: {
    fields() {

      let fields = {
        status: {
          name: "status",
          label: this.$t("page.status.select"),
          type: "radio",
          required: true,
          options: Object.keys(this.states).map(key => {
            return {
              value: key,
              text: this.states[key].label,
              info: this.states[key].text
            };
          })
        }
      };

      if (
        this.form.status === "listed" &&
        this.page.blueprint.num === "default"
      ) {
        fields.position = {
          name: "position",
          label: this.$t("page.num.select"),
          type: "select",
          empty: false,
          options: this.sortingOptions()
        };
      }

      return fields;
    }
  },
  methods: {
    sortingOptions() {
      let options = [];
      let index = 0;

      this.page.siblings.forEach(sibling => {
        if (sibling.id === this.page.id || sibling.num < 1) {
          return false;
        }

        index++;

        options.push({
          value: index,
          text: index
        });

        options.push({
          value: sibling.id,
          text: sibling.title,
          disabled: true
        });
      });

      options.push({
        value: index + 1,
        text: index + 1
      });

      return options;
    },
    open(id) {
      this.$api.pages
        .get(id, {
          select: ["id", "status", "num", "errors", "siblings", "blueprint"]
        })
        .then(page => {
          if (page.blueprint.options.changeStatus === false) {
            return this.$store.dispatch("notification/error", {
              message: this.$t("error.page.changeStatus.permission")
            });
          }

          if (page.status === "draft" && Object.keys(page.errors).length > 0) {
            return this.$store.dispatch("notification/error", {
              message: this.$t("error.page.changeStatus.incomplete"),
              details: page.errors
            });
          }

          this.states = page.blueprint.status;
          this.page = page;
          this.form.status = page.status;
          this.form.position = page.num;
          this.$refs.dialog.open();
        })
        .catch(error => {
          this.$store.dispatch('notification/error', error);
        });

    },
    submit() {
      this.$refs.form.submit();
    },
    changeStatus() {
      this.$api.pages
        .status(this.page.id, this.form.status, this.form.position || 1)
        .then(response => {
          let message = "";

          if (this.form.status === "listed" && this.page.blueprint.num === "default") {
            message = this.$t("page.status.change.result.num", {
              num: response.num
            });
          } else {
            message = this.$t("page.status.change.result", {
              status: this.states[response.status].label
            });
          }

          this.success({
            message: message,
            event: "page.changeStatus"
          });
        })
        .catch(error => {
          this.$refs.dialog.error(error.message);
        });
    }
  }
};
</script>
