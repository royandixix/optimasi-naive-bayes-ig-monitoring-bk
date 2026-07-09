import sys
import json
import math
from collections import defaultdict, Counter

def entropy(labels):
    total = len(labels)
    if total == 0:
        return 0.0

    counts = Counter(labels)
    value = 0.0

    for count in counts.values():
        probability = count / total
        value -= probability * math.log2(probability)

    return value

def split_samples(samples, ratio):
    grouped = defaultdict(list)

    for sample in samples:
        grouped[sample["label"]].append(sample)

    training = []
    testing = []

    for group in grouped.values():
        count = len(group)
        training_count = max(1, int(math.floor(count * ratio)))

        if count > 1 and training_count >= count:
            training_count = count - 1

        training.extend(group[:training_count])
        testing.extend(group[training_count:])

    if len(testing) == 0 and len(training) > 0:
        testing = training[:1]

    return training, testing

def calculate_information_gain(samples, features):
    base_entropy = entropy([sample["label"] for sample in samples])
    results = []

    for feature in features:
        groups = defaultdict(list)

        for sample in samples:
            value = str(sample["features"].get(feature, "Tidak Ada"))
            groups[value].append(sample)

        weighted_entropy = 0.0

        for group in groups.values():
            weighted_entropy += (len(group) / max(len(samples), 1)) * entropy([sample["label"] for sample in group])

        results.append({
            "feature": feature,
            "gain": round(base_entropy - weighted_entropy, 10),
            "entropy_before": round(base_entropy, 10),
            "entropy_after": round(weighted_entropy, 10),
            "values": {key: len(value) for key, value in groups.items()}
        })

    results = sorted(results, key=lambda item: item["gain"], reverse=True)

    for index, item in enumerate(results):
        item["ranking"] = index + 1

    return results

def train_naive_bayes(samples, features, classes):
    class_counts = {class_name: 0 for class_name in classes}
    feature_counts = {
        class_name: {feature: defaultdict(int) for feature in features}
        for class_name in classes
    }
    feature_values = {feature: set() for feature in features}

    for sample in samples:
        label = sample["label"]

        if label not in classes:
            continue

        class_counts[label] += 1

        for feature in features:
            value = str(sample["features"].get(feature, "Tidak Ada"))
            feature_values[feature].add(value)
            feature_counts[label][feature][value] += 1

    return {
        "total": len(samples),
        "classes": classes,
        "features": features,
        "class_counts": class_counts,
        "feature_counts": feature_counts,
        "feature_values": {feature: list(values) for feature, values in feature_values.items()}
    }

def normalize_logs(logs):
    maximum = max(logs.values())
    exponentials = {}
    total = 0.0

    for class_name, value in logs.items():
        exponentials[class_name] = math.exp(value - maximum)
        total += exponentials[class_name]

    return {
        class_name: round(value / max(total, sys.float_info.min), 6)
        for class_name, value in exponentials.items()
    }

def predict(model, features):
    logs = {}
    total_classes = len(model["classes"])

    for class_name in model["classes"]:
        class_count = model["class_counts"].get(class_name, 0)
        log_probability = math.log((class_count + 1) / (model["total"] + total_classes))

        for feature in model["features"]:
            value = str(features.get(feature, "Tidak Ada"))
            value_count = model["feature_counts"][class_name][feature].get(value, 0)
            value_cardinality = max(len(model["feature_values"].get(feature, [])), 1)
            log_probability += math.log((value_count + 1) / (class_count + value_cardinality))

        logs[class_name] = log_probability

    probabilities = normalize_logs(logs)
    predicted_class = max(probabilities, key=probabilities.get)

    return {
        "class": predicted_class,
        "probability": probabilities[predicted_class],
        "probabilities": probabilities
    }

def evaluate(samples, model, classes):
    matrix = {
        actual: {predicted: 0 for predicted in classes}
        for actual in classes
    }

    for sample in samples:
        actual = sample["label"]

        if actual not in classes:
            continue

        predicted = predict(model, sample["features"])["class"]
        matrix[actual][predicted] += 1

    total = max(len(samples), 1)
    correct = 0
    precision_total = 0.0
    recall_total = 0.0
    f1_total = 0.0

    for class_name in classes:
        tp = matrix[class_name][class_name]
        fp = sum(matrix[other][class_name] for other in classes if other != class_name)
        fn = sum(matrix[class_name][other] for other in classes if other != class_name)

        precision = tp / (tp + fp) if (tp + fp) > 0 else 0.0
        recall = tp / (tp + fn) if (tp + fn) > 0 else 0.0
        f1 = (2 * precision * recall / (precision + recall)) if (precision + recall) > 0 else 0.0

        correct += tp
        precision_total += precision
        recall_total += recall
        f1_total += f1

    return {
        "akurasi": round((correct / total) * 100, 2),
        "precision": round((precision_total / len(classes)) * 100, 2),
        "recall": round((recall_total / len(classes)) * 100, 2),
        "f1_score": round((f1_total / len(classes)) * 100, 2),
        "confusion_matrix": matrix,
        "features": model["features"]
    }

def main():
    payload = json.loads(sys.stdin.read())

    samples = payload.get("samples", [])
    features = payload.get("features", [])
    classes = payload.get("classes", ["Baik", "Perlu Pembinaan", "Bermasalah"])
    training_ratio = float(payload.get("training_ratio", 0.8))

    if len(samples) < 3:
        print(json.dumps({
            "success": False,
            "message": "Data belum cukup. Minimal dibutuhkan 3 data siswa."
        }, ensure_ascii=False))
        return

    training, testing = split_samples(samples, training_ratio)
    gain_results = calculate_information_gain(training, features)

    selected_features = [
        item["feature"]
        for item in gain_results
        if item["gain"] > 0
    ][:5]

    if len(selected_features) == 0:
        selected_features = features

    baseline_model = train_naive_bayes(training, features, classes)
    optimized_model = train_naive_bayes(training, selected_features, classes)

    predictions = []

    for sample in samples:
        baseline = predict(baseline_model, sample["features"])
        optimized = predict(optimized_model, sample["features"])

        predictions.append({
            "siswa_id": sample["siswa_id"],
            "jumlah_pelanggaran": sample["jumlah_pelanggaran"],
            "total_poin": sample["total_poin"],
            "label": sample["label"],
            "features": sample["features"],
            "baseline": baseline,
            "optimized": optimized
        })

    print(json.dumps({
        "success": True,
        "message": "Klasifikasi berhasil diproses menggunakan Python.",
        "total_samples": len(samples),
        "training_count": len(training),
        "testing_count": len(testing),
        "selected_features": selected_features,
        "gain_results": gain_results,
        "predictions": predictions,
        "baseline_evaluation": evaluate(testing, baseline_model, classes),
        "optimized_evaluation": evaluate(testing, optimized_model, classes)
    }, ensure_ascii=False))

if __name__ == "__main__":
    main()
