<template>
  <k-view v-if="system" align="center" class="k-installation-view">
    <form v-if="system.isOk && !system.isInstalled" @submit.prevent="install">
      <k-fieldset :fields="fields" v-model="user" />
      <k-button type="submit" icon="check">{{ $t("install") }}</k-button>
    </form>
    <k-text v-else-if="system.isInstalled">
      <k-headline>The panel is already installed</k-headline>
      <k-link to="/login">Login now</k-link>
    </k-text>
    <div v-else>
      <k-headline>{{ $t("installation.issues.headline") }}</k-headline>

      <ul class="k-installation-issues">
        <li v-if="requirements.php === false">
          <k-icon type="alert" />
          <span v-html="$t('installation.issues.php')" />
        </li>

        <li v-if="requirements.server === false">
          <k-icon type="alert" />
          <span v-html="$t('installation.issues.server')" />
        </li>

        <li v-if="requirements.mbstring === false">
          <k-icon type="alert" />
          <span v-html="$t('installation.issues.mbstring')" />
        </li>

        <li v-if="requirements.curl === false">
          <k-icon type="alert" />
          <span v-html="$t('installation.issues.curl')" />
        </li>

        <li v-if="requirements.accounts === false">
          <k-icon type="alert" />
          <span v-html="$t('installation.issues.accounts')" />
        </li>

        <li v-if="requirements.content === false">
          <k-icon type="alert" />
          <span v-html="$t('installation.issues.content')" />
        </li>

        <li v-if="requirements.media === false">
          <k-icon type="alert" />
          <span v-html="$t('installation.issues.media')" />
        </li>

      </ul>

      <k-button icon="refresh" @click="check"><span v-html="$t('retry')" /></k-button>

    </div>
  </k-view>
</template>

<script>
export default {
  data() {
    return {
      user: {
        email: "",
        language: "en",
        password: "",
        role: "admin"
      },
      languages: [],
      system: null
    };
  },
  computed: {
    requirements() {
      return this.system ? this.system.requirements : {};
    },
    fields() {
      return {
        email: {
          label: this.$t("user.email"),
          type: "email",
          link: false,
          placeholder: this.$t("user.email.placeholder"),
          required: true,
          autofocus: true
        },
        password: {
          label: this.$t("user.password"),
          type: "password",
          placeholder: this.$t("user.password") + " …",
          required: true
        },
        language: {
          label: this.$t("user.language"),
          type: "select",
          options: this.languages,
          icon: "globe",
          empty: false,
          required: true
        }
      };
    }
  },
  watch: {
    "user.language"(language) {
      this.$store.dispatch("translation/activate", language);
    }
  },
  created() {
    this.check();
  },
  methods: {
    install() {
      this.$api.system
        .install(this.user)
        .then(user => {
          this.$store.dispatch("user/current", user);
          this.$store.dispatch("notification/success", "Welcome!");
          this.$router.push("/");
        })
        .catch(error => {
          this.$store.dispatch("notification/error", error);
        });
    },
    check() {
      this.$store.dispatch("system/load", true).then(system => {
        if (system.isInstalled === true) {
          this.$router.push("/login");
        }

        this.$api.translations.options().then(languages => {
          this.languages = languages;

          this.system = system;
          this.$store.dispatch("title", this.$t("view.installation"));
        });
      });
    }
  }
};
</script>

<style lang="scss">
.k-installation-view .k-button {
  display: block;
  margin-top: 1.5rem;
}

.k-installation-issues {
  line-height: 1.5em;
  font-size: $font-size-small;
}
.k-installation-issues li {
  position: relative;
  padding: 1.5rem;
  padding-left: 3.5rem;
  background: $color-white;
}
.k-installation-issues .k-icon {
  position: absolute;
  top: calc(1.5rem + 2px);
  left: 1.5rem;
}
.k-installation-issues .k-icon svg * {
  fill: $color-negative;
}
.k-installation-issues li:not(:last-child) {
  margin-bottom: 2px;
}
.k-installation-issues li code {
  font: inherit;
  color: $color-negative;
}

.k-installation-view .k-button[type="submit"] {
  padding: 1rem;
  margin-left: -1rem;
}
</style>
