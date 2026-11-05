// Rule checks run on yesterday's ad-level insights. Thresholds come from the user's settings.

const MIN_IMPRESSIONS = 1000; // don't judge an ad on too little data

/**
 * @returns {Array<{adId, ad, issue, detail, suggestion, autoPause}>}
 */
export function evaluateRules(rows, settings) {
  const findings = [];
  const minSpend = Number(settings.minSpendForPause) || (Number(settings.maxCpc) ? Number(settings.maxCpc) * 3 : 0);

  for (const r of rows) {
    if (!r.adId) continue;
    const enoughData = r.impressions >= MIN_IMPRESSIONS;
    const add = (issue, detail, suggestion, autoPause = false) =>
      findings.push({ adId: r.adId, ad: r.ad, adset: r.adset, campaign: r.campaign, issue, detail, suggestion, autoPause });

    if (settings.maxCpm > 0 && enoughData && r.cpm > settings.maxCpm) {
      add("High CPM", `CPM ${r.cpm} > limit ${settings.maxCpm}`, "Broaden the audience, test Advantage+ placements, or refresh the creative.");
    }

    if (settings.maxCpc > 0 && r.clicks >= 10 && r.cpc > settings.maxCpc) {
      add("High CPC", `CPC ${r.cpc} > limit ${settings.maxCpc}`, "Improve the hook in the first line and the image; test a new audience.");
    }

    if (settings.minCtr > 0 && enoughData && r.ctr < settings.minCtr) {
      // Very weak ads that have already spent enough are candidates for automatic pausing.
      const veryWeak = r.clicks === 0 || r.ctr < settings.minCtr / 2;
      const spentEnough = minSpend > 0 && r.spend >= minSpend;
      add(
        "Low CTR",
        `CTR ${r.ctr}% < target ${settings.minCtr}% (spend ${r.spend})`,
        veryWeak ? "Pause this ad and launch a new creative variant." : "Test a new creative or headline.",
        veryWeak && spentEnough,
      );
    }

    if (settings.maxFrequency > 0 && r.frequency > settings.maxFrequency) {
      add("Ad fatigue", `Frequency ${r.frequency} > ${settings.maxFrequency}`, "Rotate in fresh creatives or expand the audience.");
    }
  }
  return findings;
}
