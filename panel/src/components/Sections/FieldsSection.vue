<template>
  <section class="k-fields-section">
    <template v-if="issue">
      <k-headline class="k-fields-issue-headline">Error</k-headline>
      <k-box :text="issue.message" theme="negative" />
    </template>
    <k-form
      v-else
      :fields="fields"
      :validate="true"
      v-model="values"
      @input="input"
      @submit="onSubmit"
    />
  </section>
</template>

<script>
import SectionMixin from "@/mixins/section.js";

export default {
  mixins: [SectionMixin],
  data() {
    return {
      errors: [],
      fields: {},
      isLoading: true,
      stored: {},
      values: {},
      issue: null
    };
  },
  computed: {
    id() {
      return this.$cache.id(this.$route, this.$store);
    },
    language() {
      return this.$store.state.languages.current;
    }
  },
  watch: {
    language() {
      this.fetch();
    }
  },
  created: function() {
    this.fetch();
    this.$events.$on("form.reset", this.fetch);
    this.$events.$on("model.update", this.fetch);
  },
  destroyed: function() {
    this.$events.$off("form.reset", this.fetch);
    this.$events.$off("model.update", this.fetch);
  },
  methods: {
    input(values) {
      this.$cache.set(this.id, values);
      this.$events.$emit("form.change");
    },
    fetch() {
      this.$api
        .get(this.parent + "/sections/" + this.name)
        .then(response => {
          this.errors = response.errors;
          this.fields = response.fields;

          this.stored = response.data;
          this.values = Object.assign(
            {},
            response.data,
            this.$cache.get(this.id) || {}
          );
          this.$events.$emit("form.change");
          this.isLoading = false;
        })
        .catch(error => {
          this.issue = error;
          this.isLoading = false;
        });
    },
    onSubmit($event) {
      this.$events.$emit("keydown.cmd.s", $event);
    }
  }
};
</script>

<style>
.k-fields-issue-headline {
  margin-bottom: .5rem;
}
.k-fields-section input[type="submit"] {
  display: none;
}
</style>
