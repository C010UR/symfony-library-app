function resolveTransition(to, from) {
  if (from.meta.transitionType === 'Auth' && to.meta.transitionType === 'Auth') {
    return from.meta.transitionLevel - to.meta.transitionLevel > 0
      ? 'slide-to-bottom'
      : 'slide-to-top';
  }

  return null;
}

export { resolveTransition };
